<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\ClientBillingMethod;
use App\Models\ClientInvoiceDetail;
use App\Models\ClientProject;
use App\Models\Freelancer;
use App\Models\ProjectOffer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkspaceController extends Controller
{
    public function landing(Request $request)
    {
        if ($request->user()) {
            return redirect()->route('workspace.dashboard');
        }

        return view('workspace.index', [
            'featuredFreelancers' => Freelancer::featured()->orderBy('name')->take(4)->get(),
        ]);
    }

    public function welcome(Request $request)
    {
        $user = $request->user();

        return view('workspace.app.welcome', [
            'user' => $user,
            'projectCount' => $user->projects()->count(),
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $snapshot = $this->buildSnapshot($user);

        if ($snapshot['primaryOffer']) {
            return view('workspace.app.dashboard-live', $snapshot);
        }

        return view('workspace.app.dashboard', $snapshot);
    }

    public function dashboardLive(Request $request)
    {
        $snapshot = $this->buildSnapshot($request->user());

        if (! $snapshot['primaryOffer']) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('info', 'Create a brief and send an offer to unlock the live dashboard.');
        }

        return view('workspace.app.dashboard-live', $snapshot);
    }

    public function hireFlow(Request $request)
    {
        $user = $request->user();
        $project = $this->resolveProject($user, $request->query('project'));
        $offer = $project->exists ? $project->offers()->with('freelancer')->latest()->first() : null;
        $selectedFreelancer = $this->resolveSelectedFreelancer(
            $request->query('freelancer'),
            $offer?->freelancer_id,
        );

        return view('workspace.app.hire-flow', [
            'user' => $user,
            'project' => $project,
            'offer' => $offer,
            'selectedFreelancer' => $selectedFreelancer,
            'freelancers' => Freelancer::featured()->orderBy('name')->take(4)->get(),
            'experienceOptions' => ['Entry', 'Intermediate', 'Expert'],
            'timeframeOptions' => ['Less than 1 week', 'Less than 1 month', '1–3 months', '3–6 months', 'More than 6 months'],
            'specialtyOptions' => ['Front-end development', 'Back-end development', 'Full stack development', 'Mobile app development', 'UI/UX design', 'E-commerce development'],
        ]);
    }

    public function storeBrief(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'selected_freelancer_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'experience_level' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:100'],
            'specialty' => ['required', 'string', 'max:100'],
            'action' => ['nullable', 'string'],
        ]);

        $project = $this->resolveProject($user, $data['project_id'] ?? null);

        if (! $project->exists) {
            $project->user()->associate($user);
        }

        $project->fill([
            'title' => $data['title'],
            'description' => $data['description'],
            'experience_level' => $data['experience_level'],
            'timeframe' => $data['timeframe'],
            'specialty' => $data['specialty'],
            'status' => in_array($project->status, ['pending', 'active', 'completed'], true) ? $project->status : 'draft',
            'last_saved_at' => now(),
        ]);

        $project->save();

        if (($data['action'] ?? null) === 'continue') {
            $offerData = $request->validate([
                'selected_freelancer_id' => ['nullable', 'integer'],
                'freelancer_email' => ['required', 'string', 'email', 'max:255'],
                'hourly_rate' => ['required', 'numeric', 'min:1'],
                'weekly_limit' => ['required', 'integer', 'min:1'],
            ]);

            $offer = $this->upsertOfferForProject($user, $project, $offerData);

            return redirect()
                ->route('workspace.billing-method', ['offer' => $offer->id])
                ->with('success', 'Project brief and offer saved. Add or confirm billing next.');
        }

        return redirect()
            ->route('workspace.hire-flow', array_filter([
                'project' => $project->id,
                'freelancer' => $data['selected_freelancer_id'] ?? null,
            ]))
            ->with('success', 'Project brief saved.');
    }

    public function inviteOffer(Request $request)
    {
        $user = $request->user();
        $project = $this->resolveProject($user, $request->query('project'));

        if (! $project->exists) {
            return redirect()
                ->route('workspace.hire-flow')
                ->with('info', 'Project brief and offer are now combined on one page.');
        }

        $offer = $project->offers()->latest()->first();

        return redirect()
            ->route('workspace.hire-flow', array_filter([
                'project' => $project->id,
                'freelancer' => $offer?->freelancer_id,
            ]))
            ->with('info', 'Project brief and offer are now combined on one page.');
    }

    public function storeOffer(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'project_id' => ['required', 'integer'],
            'selected_freelancer_id' => ['nullable', 'integer'],
            'freelancer_email' => ['required', 'string', 'email', 'max:255'],
            'hourly_rate' => ['required', 'numeric', 'min:1'],
            'weekly_limit' => ['required', 'integer', 'min:1'],
            'manual_time' => ['nullable', 'boolean'],
            'multi_offer' => ['nullable', 'boolean'],
        ]);

        $project = $this->resolveProject($user, $data['project_id']);

        if (! $project->exists) {
            return redirect()
                ->route('workspace.hire-flow')
                ->with('info', 'Save a project brief before you create an offer.');
        }

        $offer = $this->upsertOfferForProject($user, $project, $data);

        $offer->manual_time = (bool) ($data['manual_time'] ?? true);
        $offer->multi_offer = (bool) ($data['multi_offer'] ?? false);
        $offer->save();

        return redirect()
            ->route('workspace.billing-method', ['offer' => $offer->id])
            ->with('success', 'Offer saved. Add or confirm the billing method next.');
    }

    public function billingMethod(Request $request)
    {
        $user = $request->user();
        $offer = $this->resolveOffer($user, $request->query('offer'));

        return view('workspace.app.billing-method', [
            'offer' => $offer,
            'billingMethods' => $user->billingMethods()->orderByDesc('is_default')->latest()->get(),
            'defaultBillingMethod' => $user->defaultBillingMethod,
        ]);
    }

    public function storeBillingMethod(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'offer_id' => ['nullable', 'integer'],
            'method_type' => ['required', Rule::in(['PayPal', 'Visa', 'Mastercard'])],
            'label' => ['required', 'string', 'max:255'],
            'last_four' => [
                'nullable',
                'string',
                'max:4',
                'regex:/^[0-9]{4}$/',
                Rule::requiredIf(fn (): bool => $request->input('method_type') !== 'PayPal'),
            ],
            'set_default' => ['nullable', 'boolean'],
        ]);

        $offer = null;

        if (! empty($data['offer_id'])) {
            $offer = $this->resolveOffer($user, $data['offer_id']);

            if (! $offer) {
                return redirect()
                    ->route('workspace.hire-flow')
                    ->with('info', 'Create the project brief and offer before you add a billing method.');
            }
        }

        $billing = null;

        DB::transaction(function () use ($user, $data, $offer, &$billing) {
            $setDefault = (bool) ($data['set_default'] ?? false) || ! $user->billingMethods()->exists();

            if ($setDefault) {
                $user->billingMethods()->update(['is_default' => false]);
            }

            $billing = $user->billingMethods()->create([
                'method_type' => $data['method_type'],
                'label' => $data['label'],
                'last_four' => $data['method_type'] === 'PayPal' ? null : $data['last_four'],
                'is_default' => $setDefault,
            ]);

            if ($offer) {
                $offer->update([
                    'billing_method' => $billing->display_label,
                    'status' => 'pending',
                ]);
            }
        });

        if ($offer) {
            return redirect()
                ->route('workspace.project-pending')
                ->with('success', 'Billing method saved for this offer.');
        }

        return redirect()
            ->route('workspace.billing-method')
            ->with('success', 'Billing method added.');
    }

    public function setPrimaryBillingMethod(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'billing_method_id' => ['required', 'integer'],
            'offer_id' => ['nullable', 'integer'],
        ]);

        $billingMethod = $user->billingMethods()->find($data['billing_method_id']);

        if (! $billingMethod) {
            return redirect()
                ->route('workspace.billing-method')
                ->with('info', 'That billing method could not be found.');
        }

        $offer = null;

        if (! empty($data['offer_id'])) {
            $offer = $this->resolveOffer($user, $data['offer_id']);
        }

        DB::transaction(function () use ($user, $billingMethod, $offer) {
            $user->billingMethods()->update(['is_default' => false]);
            $billingMethod->update(['is_default' => true]);

            if ($offer) {
                $offer->update([
                    'billing_method' => $billingMethod->display_label,
                ]);
            }
        });

        if ($offer) {
            return redirect()
                ->route('workspace.project-pending')
                ->with('success', 'Primary billing method updated.');
        }

        return redirect()
            ->route('workspace.billing-method')
            ->with('success', 'Primary billing method updated.');
    }

    public function destroyBillingMethod(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'billing_method_id' => ['required', 'integer'],
            'offer_id' => ['nullable', 'integer'],
        ]);

        $billingMethod = $user->billingMethods()->find($data['billing_method_id']);

        if (! $billingMethod) {
            return redirect()
                ->route('workspace.billing-method')
                ->with('info', 'That billing method could not be found.');
        }

        $deletedLabel = $billingMethod->display_label;
        $wasDefault = $billingMethod->is_default;

        DB::transaction(function () use ($user, $billingMethod, $deletedLabel, $wasDefault) {
            $billingMethod->delete();

            $replacement = null;

            if ($wasDefault) {
                $replacement = $user->billingMethods()->latest()->first();

                if ($replacement) {
                    $replacement->update(['is_default' => true]);
                }
            }

            ProjectOffer::query()
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->where('billing_method', $deletedLabel)
                ->update([
                    'billing_method' => $replacement?->display_label,
                ]);
        });

        if (! empty($data['offer_id'])) {
            return redirect()
                ->route('workspace.project-pending')
                ->with('success', 'Billing method removed.');
        }

        return redirect()
            ->route('workspace.billing-method')
            ->with('success', 'Billing method removed.');
    }

    public function invoiceDetails(Request $request)
    {
        return view('workspace.app.invoice-details', [
            'invoiceDetail' => $request->user()->invoiceDetail,
        ]);
    }

    public function storeInvoiceDetails(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        ClientInvoiceDetail::updateOrCreate(
            ['user_id' => $user->id],
            $data,
        );

        return redirect()
            ->route('workspace.invoice-details')
            ->with('success', 'Invoice details saved.');
    }

    public function projectPending(Request $request)
    {
        $snapshot = $this->buildSnapshot($request->user());
        $offer = $snapshot['pendingOffer'] ?: $snapshot['primaryOffer'];

        if (! $offer) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('info', 'No pending offer is available yet.');
        }

        if ($offer->status === 'active') {
            return redirect()->route('workspace.project-active');
        }

        $billingMethod = $offer->billing_method ?: $request->user()->defaultBillingMethod?->display_label;

        return view('workspace.app.project-pending', [
            'offer' => $offer,
            'project' => $offer->project,
            'billingMethod' => $billingMethod,
            'billingVerified' => ! empty($billingMethod),
        ]);
    }

    public function activateProject(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'offer_id' => ['required', 'integer'],
        ]);

        $offer = $this->resolveOffer($user, $data['offer_id']);

        if (! $offer) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('info', 'We could not find that offer.');
        }

        DB::transaction(function () use ($offer) {
            $offer->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            $offer->project->update([
                'status' => 'active',
            ]);

            if (! $offer->project->messages()->exists()) {
                $offer->project->messages()->createMany([
                    [
                        'project_offer_id' => $offer->id,
                        'sender_type' => 'system',
                        'sender_name' => 'System',
                        'message' => 'The contract is now active. Keep billing verified and use this room for project updates.',
                        'sent_at' => now(),
                    ],
                    [
                        'project_offer_id' => $offer->id,
                        'sender_type' => 'freelancer',
                        'sender_name' => $offer->freelancer_display_name,
                        'message' => 'Thanks for the offer. I have started reviewing the project brief and will share the first update soon.',
                        'sent_at' => now(),
                    ],
                ]);
            }
        });

        return redirect()
            ->route('workspace.project-active')
            ->with('success', 'The contract is now active.');
    }

    public function projectActive(Request $request)
    {
        $snapshot = $this->buildSnapshot($request->user());
        $offer = $snapshot['activeOffer'] ?: $snapshot['primaryOffer'];

        if (! $offer) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('info', 'No active contract is available yet.');
        }

        if ($offer->status !== 'active') {
            return redirect()->route('workspace.project-pending');
        }

        return view('workspace.app.project-active', [
            'offer' => $offer,
            'project' => $offer->project,
            'billingMethod' => $offer->billing_method ?: $request->user()->defaultBillingMethod?->display_label,
        ]);
    }

    public function closeProject(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'offer_id' => ['required', 'integer'],
        ]);

        $offer = $this->resolveOffer($user, $data['offer_id']);

        if (! $offer) {
            return redirect()->route('workspace.dashboard');
        }

        DB::transaction(function () use ($offer) {
            $offer->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            $offer->project->update([
                'status' => 'completed',
            ]);

            $offer->project->messages()->create([
                'project_offer_id' => $offer->id,
                'sender_type' => 'system',
                'sender_name' => 'System',
                'message' => 'The contract was marked as completed from the client workspace.',
                'sent_at' => now(),
            ]);
        });

        return redirect()
            ->route('workspace.dashboard')
            ->with('success', 'The contract has been marked as completed.');
    }

    public function messages(Request $request)
    {
        $snapshot = $this->buildSnapshot($request->user());
        $offer = $snapshot['activeOffer'] ?: $snapshot['pendingOffer'] ?: $snapshot['primaryOffer'];

        return view('workspace.app.messages', [
            'offer' => $offer,
            'project' => $offer?->project,
            'messages' => $offer?->project?->messages()->orderBy('sent_at')->get() ?? collect(),
        ]);
    }

    public function storeMessage(Request $request)
    {
        $user = $request->user();
        $snapshot = $this->buildSnapshot($user);
        $offer = $snapshot['activeOffer'] ?: $snapshot['pendingOffer'] ?: $snapshot['primaryOffer'];

        if (! $offer) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('info', 'Create a project and send an offer before messaging.');
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $offer->project->messages()->create([
            'project_offer_id' => $offer->id,
            'sender_type' => 'client',
            'sender_name' => $user->name,
            'message' => $data['message'],
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('workspace.messages')
            ->with('success', 'Message sent.');
    }

    public function reports(Request $request)
    {
        $snapshot = $this->buildSnapshot($request->user());
        $offer = $snapshot['activeOffer'] ?: $snapshot['pendingOffer'] ?: $snapshot['primaryOffer'];
        $billingMethod = $request->user()->defaultBillingMethod?->display_label ?: $offer?->billing_method;

        return view('workspace.app.reports', array_merge($snapshot, [
            'billingMethod' => $billingMethod,
            'estimatedWeeklySpend' => $offer ? $offer->weekly_amount : 0,
        ]));
    }

    public function settings(Request $request)
    {
        return view('workspace.app.settings', [
            'user' => $request->user(),
            'billingMethod' => $request->user()->defaultBillingMethod,
            'invoiceDetail' => $request->user()->invoiceDetail,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'notify_messages' => ['nullable', 'boolean'],
            'notify_reports' => ['nullable', 'boolean'],
            'reminder_frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
        ]);

        $user->update([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'notify_messages' => (bool) ($data['notify_messages'] ?? false),
            'notify_reports' => (bool) ($data['notify_reports'] ?? false),
            'reminder_frequency' => $data['reminder_frequency'],
        ]);

        return redirect()
            ->route('workspace.settings')
            ->with('success', 'Settings updated.');
    }

    protected function buildSnapshot(User $user): array
    {
        $projects = $user->projects()
            ->with(['offers.freelancer', 'messages'])
            ->latest()
            ->get();

        $primaryProject = $projects->first();

        $activeOffer = ProjectOffer::query()
            ->where('status', 'active')
            ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
            ->with(['project', 'freelancer'])
            ->latest()
            ->first();

        $pendingOffer = ProjectOffer::query()
            ->where('status', 'pending')
            ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
            ->with(['project', 'freelancer'])
            ->latest()
            ->first();

        $primaryOffer = $activeOffer ?: $pendingOffer ?: $primaryProject?->latestOffer?->loadMissing(['project', 'freelancer']);

        return [
            'user' => $user,
            'projects' => $projects,
            'draftProject' => $projects->firstWhere('status', 'draft'),
            'primaryProject' => $primaryOffer?->project ?: $primaryProject,
            'primaryOffer' => $primaryOffer,
            'pendingOffer' => $pendingOffer,
            'activeOffer' => $activeOffer,
            'billingMethod' => $user->defaultBillingMethod,
            'invoiceDetail' => $user->invoiceDetail,
            'featuredFreelancers' => Freelancer::featured()->orderBy('name')->take(4)->get(),
            'projectCount' => $projects->count(),
            'pendingCount' => ProjectOffer::query()
                ->where('status', 'pending')
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->count(),
            'activeCount' => ProjectOffer::query()
                ->where('status', 'active')
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->count(),
        ];
    }

    protected function resolveProject(User $user, mixed $projectId = null): ClientProject
    {
        if ($projectId) {
            $project = $user->projects()->with(['offers.freelancer', 'messages'])->find($projectId);

            if ($project) {
                return $project;
            }
        }

        return $user->projects()
            ->with(['offers.freelancer', 'messages'])
            ->whereIn('status', ['draft', 'pending', 'active'])
            ->latest()
            ->first() ?? new ClientProject([
                'title' => '',
                'description' => '',
                'experience_level' => 'Intermediate',
                'timeframe' => 'Less than 1 month',
                'specialty' => 'Full stack development',
                'status' => 'draft',
            ]);
    }

    protected function resolveOffer(User $user, mixed $offerId = null): ?ProjectOffer
    {
        $query = ProjectOffer::query()
            ->whereHas('project', fn ($builder) => $builder->where('user_id', $user->id))
            ->with(['project', 'freelancer']);

        if ($offerId) {
            return $query->find($offerId);
        }

        return $query->latest()->first();
    }

    protected function resolveSelectedFreelancer(mixed $preferredFreelancerId = null, mixed $fallbackFreelancerId = null): ?Freelancer
    {
        $freelancerId = $preferredFreelancerId ?: $fallbackFreelancerId;

        if (! $freelancerId) {
            return null;
        }

        return Freelancer::withTrashed()->find($freelancerId);
    }

    protected function upsertOfferForProject(User $user, ClientProject $project, array $data): ProjectOffer
    {
        $freelancer = $this->resolveOfferFreelancer(
            $data['selected_freelancer_id'] ?? null,
            (string) $data['freelancer_email'],
            $project,
            (float) $data['hourly_rate'],
        );

        $offer = $project->offers()->latest()->first() ?? new ProjectOffer();
        $offer->project()->associate($project);
        $offer->freelancer_id = $freelancer->id;
        $offer->freelancer_email = Str::lower((string) $data['freelancer_email']);
        $offer->role = $project->specialty ?: 'Freelancer';
        $offer->hourly_rate = $data['hourly_rate'];
        $offer->weekly_limit = (int) ($data['weekly_limit'] ?? 40);
        $offer->manual_time = (bool) ($data['manual_time'] ?? true);
        $offer->multi_offer = (bool) ($data['multi_offer'] ?? false);
        $offer->status = 'pending';
        $offer->sent_at = now();
        $offer->billing_method = $user->defaultBillingMethod?->display_label ?: $offer->billing_method;
        $offer->save();

        $project->update([
            'status' => 'pending',
            'last_saved_at' => now(),
        ]);

        return $offer;
    }

    protected function resolveOfferFreelancer(mixed $selectedFreelancerId, string $email, ClientProject $project, float $hourlyRate): Freelancer
    {
        if ($selectedFreelancerId) {
            $selectedFreelancer = Freelancer::withTrashed()->find($selectedFreelancerId);

            if ($selectedFreelancer) {
                if (method_exists($selectedFreelancer, 'trashed') && $selectedFreelancer->trashed()) {
                    $selectedFreelancer->restore();
                }

                if (! filled($selectedFreelancer->contact_email)) {
                    $selectedFreelancer->contact_email = Str::lower(trim($email));
                    $selectedFreelancer->save();
                }

                return $selectedFreelancer;
            }
        }

        return $this->findOrCreateFreelancerForEmail($email, $project, $hourlyRate);
    }

    protected function findOrCreateFreelancerForEmail(string $email, ClientProject $project, float $hourlyRate): Freelancer
    {
        $email = Str::lower(trim($email));

        $freelancer = Freelancer::withTrashed()
            ->where('contact_email', $email)
            ->first();

        if (! $freelancer) {
            $freelancer = new Freelancer();
            $freelancer->slug = Freelancer::generateUniqueSlug(Str::before($email, '@'));
            $freelancer->name = Str::headline(str_replace(['.', '_', '-'], ' ', Str::before($email, '@')) ?: $email);
            $freelancer->title = $project->specialty ?: 'Freelancer';
            $freelancer->hourly_rate = $hourlyRate;
            $freelancer->overview = 'Email-based freelancer invite';
            $freelancer->bio = 'Freelancer created from the client offer flow.';
            $freelancer->avatar = 'avatar-jade.svg';
            $freelancer->status = 'active';
            $freelancer->is_featured = false;
        }

        if (method_exists($freelancer, 'trashed') && $freelancer->trashed()) {
            $freelancer->restore();
        }

        $freelancer->contact_email = $email;
        $freelancer->status = $freelancer->status ?: 'active';
        $freelancer->avatar = $freelancer->avatar ?: 'avatar-jade.svg';

        if (! $freelancer->hourly_rate || (float) $freelancer->hourly_rate <= 0) {
            $freelancer->hourly_rate = $hourlyRate;
        }

        if (! filled($freelancer->title)) {
            $freelancer->title = $project->specialty ?: 'Freelancer';
        }

        $freelancer->save();

        return $freelancer;
    }
}

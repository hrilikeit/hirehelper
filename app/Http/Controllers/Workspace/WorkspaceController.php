<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\ClientBillingMethod;
use App\Models\ClientProject;
use App\Models\Freelancer;
use App\Models\ProjectMessage;
use App\Models\ProjectOffer;
use App\Models\User;
use Database\Seeders\FreelancerSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkspaceController extends Controller
{
    public function landing(Request $request)
    {
        $this->ensureFreelancers();

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

        return view('workspace.app.hire-flow', [
            'user' => $user,
            'project' => $project,
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
            return redirect()
                ->route('workspace.invite-offer', ['project' => $project->id])
                ->with('success', 'Brief saved. Next, choose a freelancer and send an offer.');
        }

        return redirect()
            ->route('workspace.hire-flow', ['project' => $project->id])
            ->with('success', 'Brief saved.');
    }

    public function inviteOffer(Request $request)
    {
        $this->ensureFreelancers();

        $user = $request->user();
        $project = $this->resolveProject($user, $request->query('project'));

        if (! $project->exists) {
            return redirect()
                ->route('workspace.hire-flow')
                ->with('info', 'Save a project brief before you create an offer.');
        }

        $offer = $project->offers()->latest()->first();
        $freelancers = Freelancer::featured()->orderBy('name')->get();

        return view('workspace.app.invite-offer', [
            'project' => $project,
            'offer' => $offer,
            'freelancers' => $freelancers,
            'selectedFreelancerId' => old('freelancer_id', $offer?->freelancer_id ?? $freelancers->first()?->id),
        ]);
    }

    public function storeOffer(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'project_id' => ['required', 'integer'],
            'freelancer_id' => ['required', 'integer', 'exists:freelancers,id'],
            'role' => ['required', 'string', 'max:255'],
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

        $offer = $project->offers()->latest()->first() ?? new ProjectOffer();
        $offer->project()->associate($project);
        $offer->freelancer_id = $data['freelancer_id'];
        $offer->role = $data['role'];
        $offer->hourly_rate = $data['hourly_rate'];
        $offer->weekly_limit = $data['weekly_limit'];
        $offer->manual_time = (bool) ($data['manual_time'] ?? false);
        $offer->multi_offer = (bool) ($data['multi_offer'] ?? false);
        $offer->status = 'pending';
        $offer->sent_at = now();
        $offer->save();

        $project->update([
            'status' => 'pending',
            'last_saved_at' => now(),
        ]);

        return redirect()
            ->route('workspace.billing-method', ['offer' => $offer->id])
            ->with('success', 'Offer saved. Add a billing method to complete the hiring flow.');
    }

    public function billingMethod(Request $request)
    {
        $user = $request->user();
        $offer = $this->resolveOffer($user, $request->query('offer'));

        if (! $offer) {
            return redirect()
                ->route('workspace.invite-offer')
                ->with('info', 'Create an offer before you choose a billing method.');
        }

        return view('workspace.app.billing-method', [
            'offer' => $offer,
            'billingMethod' => old('billing_method', $offer->billing_method ?: $user->defaultBillingMethod?->method_type ?: 'Credit or Debit Card'),
        ]);
    }

    public function storeBillingMethod(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'offer_id' => ['required', 'integer'],
            'billing_method' => ['required', Rule::in(['Credit or Debit Card', 'PayPal'])],
        ]);

        $offer = $this->resolveOffer($user, $data['offer_id']);

        if (! $offer) {
            return redirect()
                ->route('workspace.invite-offer')
                ->with('info', 'Create an offer before you choose a billing method.');
        }

        DB::transaction(function () use ($user, $offer, $data) {
            $user->billingMethods()->update(['is_default' => false]);

            $billing = $user->billingMethods()->updateOrCreate(
                ['method_type' => $data['billing_method']],
                [
                    'label' => $data['billing_method'],
                    'last_four' => $data['billing_method'] === 'Credit or Debit Card' ? '4242' : null,
                    'is_default' => true,
                ]
            );

            $offer->update([
                'billing_method' => $billing->method_type,
                'status' => 'pending',
            ]);
        });

        return redirect()
            ->route('workspace.dashboard-live')
            ->with('success', 'Billing method saved.');
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

        return view('workspace.app.project-pending', [
            'offer' => $offer,
            'project' => $offer->project,
            'billingMethod' => $offer->billing_method ?: $request->user()->defaultBillingMethod?->method_type,
            'billingVerified' => ! empty($offer->billing_method ?: $request->user()->defaultBillingMethod?->method_type),
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
                        'sender_name' => $offer->freelancer->name,
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
            'billingMethod' => $offer->billing_method ?: $request->user()->defaultBillingMethod?->method_type,
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
        $billingMethod = $request->user()->defaultBillingMethod?->method_type ?: $offer?->billing_method;

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
                'title' => 'HireHelper.ai client dashboard rebuild',
                'description' => 'Design and implement the signed-in client experience for HireHelper.ai. The scope includes dashboard UX, project brief setup, billing setup, and contract management.',
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

    protected function ensureFreelancers(): void
    {
        if (! Freelancer::query()->exists()) {
            app(FreelancerSeeder::class)->run();
        }
    }
}

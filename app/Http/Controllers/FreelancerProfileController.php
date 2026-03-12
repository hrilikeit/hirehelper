<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Contracts\View\View;

class FreelancerProfileController extends Controller
{
    public function showById(int $freelancer): View
    {
        $record = Freelancer::query()
            ->where('status', 'active')
            ->with(['reviews' => fn ($query) => $query->orderByDesc('date_to')->orderByDesc('id')])
            ->findOrFail($freelancer);

        return $this->renderProfile($record);
    }

    public function showBySlug(string $slug): View
    {
        $record = Freelancer::query()
            ->where('status', 'active')
            ->where('slug', $slug)
            ->with(['reviews' => fn ($query) => $query->orderByDesc('date_to')->orderByDesc('id')])
            ->firstOrFail();

        return $this->renderProfile($record);
    }

    protected function renderProfile(Freelancer $freelancer): View
    {
        return view('site.freelancer-profile', [
            'freelancer' => $freelancer,
            'hireUrl' => auth()->check() ? route('workspace.hire-flow') : route('client.register'),
        ]);
    }
}

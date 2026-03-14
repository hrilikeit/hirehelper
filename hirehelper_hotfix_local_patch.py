from pathlib import Path
import sys

ROOT = Path('.')


def must_read(rel):
    p = ROOT / rel
    if not p.exists():
        print(f'MISSING: {rel}', file=sys.stderr)
        sys.exit(1)
    return p.read_text(encoding='utf-8')


def write(rel, text):
    (ROOT / rel).write_text(text, encoding='utf-8')


def replace_exact(rel, old, new):
    text = must_read(rel)
    if new in text:
        return False
    if old not in text:
        print(f'PATTERN NOT FOUND in {rel}', file=sys.stderr)
        sys.exit(1)
    write(rel, text.replace(old, new))
    return True

changed = []

# 1) Stop admin freelancer view from crashing when named public routes are missing.
old = """    public function publicProfileUrl(): string\n    {\n        if (filled($this->slug)) {\n            return route('freelancers.show', ['slug' => $this->slug]);\n        }\n\n        return route('freelancers.show-id', ['freelancer' => $this->getKey()]);\n    }\n"""
new = """    public function publicProfileUrl(): string\n    {\n        if (filled($this->slug)) {\n            if (\\Illuminate\\Support\\Facades\\Route::has('freelancers.show')) {\n                return route('freelancers.show', ['slug' => $this->slug]);\n            }\n\n            return url('/freelancers/' . $this->slug);\n        }\n\n        if (\\Illuminate\\Support\\Facades\\Route::has('freelancers.show-id')) {\n            return route('freelancers.show-id', ['freelancer' => $this->getKey()]);\n        }\n\n        return url('/freelancers/' . $this->getKey());\n    }\n"""
if replace_exact('app/Models/Freelancer.php', old, new):
    changed.append('app/Models/Freelancer.php')

# 2) Wire invoice details page into routes with a working GET + POST flow.
routes_old = """    Route::view('/billing-method.html', 'workspace.app.billing-method')->name('billing-method');\n    Route::view('/project-pending.html', 'workspace.app.project-pending')->name('project-pending');\n"""
routes_new = """    Route::view('/billing-method.html', 'workspace.app.billing-method')->name('billing-method');\n    Route::get('/invoice-details.html', function () {\n        $invoiceDetail = (object) session('invoice_details', []);\n\n        return view('workspace.app.invoice-details', compact('invoiceDetail'));\n    })->name('invoice-details');\n    Route::post('/invoice-details.html', function (\\Illuminate\\Http\\Request $request) {\n        $data = $request->validate([\n            'company_name' => ['required', 'string', 'max:255'],\n            'vat_number' => ['nullable', 'string', 'max:255'],\n            'contact_name' => ['nullable', 'string', 'max:255'],\n            'billing_email' => ['required', 'email', 'max:255'],\n            'address_line_1' => ['nullable', 'string', 'max:255'],\n            'address_line_2' => ['nullable', 'string', 'max:255'],\n            'city' => ['nullable', 'string', 'max:255'],\n            'postal_code' => ['nullable', 'string', 'max:255'],\n            'country' => ['nullable', 'string', 'max:255'],\n        ]);\n\n        session(['invoice_details' => $data]);\n\n        return redirect()->route('workspace.invoice-details')->with('success', 'Invoice details saved.');\n    })->name('invoice-details.store');\n    Route::view('/project-pending.html', 'workspace.app.project-pending')->name('project-pending');\n"""
if replace_exact('routes/web.php', routes_old, routes_new):
    changed.append('routes/web.php')

# 3) Add invoice button to dashboard.
dashboard_old = '<a class="button button-primary" href="hire-flow.html">New project</a>'
dashboard_new = '<div class="button-row"><a class="button button-primary" href="hire-flow.html">New project</a><a class="button button-secondary" href="invoice-details.html">Invoice details</a></div>'
if replace_exact('resources/views/workspace/app/dashboard.blade.php', dashboard_old, dashboard_new):
    changed.append('resources/views/workspace/app/dashboard.blade.php')

# 4) Add invoice button to live dashboard too.
if replace_exact('resources/views/workspace/app/dashboard-live.blade.php', dashboard_old, dashboard_new):
    changed.append('resources/views/workspace/app/dashboard-live.blade.php')

# 5) Add invoice row into settings.
settings_old = """        <div class=\"setting-row\">\n          <div>\n            <strong>Billing method</strong>\n            <span>Manage card or PayPal connection for the signed-in client workspace.</span>\n          </div>\n          <a class=\"cta-link\" href=\"billing-method.html\">Manage</a>\n        </div>\n        <div class=\"setting-row\">\n          <div>\n            <strong>Workspace reset</strong>\n            <span>Clear saved project data and return to a clean workspace state.</span>\n          </div>\n          <a class=\"cta-link\" href=\"dashboard.html\" data-reset-workspace>Reset</a>\n        </div>\n"""
settings_new = """        <div class=\"setting-row\">\n          <div>\n            <strong>Billing method</strong>\n            <span>Manage card or PayPal connection for the signed-in client workspace.</span>\n          </div>\n          <a class=\"cta-link\" href=\"billing-method.html\">Manage</a>\n        </div>\n        <div class=\"setting-row\">\n          <div>\n            <strong>Invoice details</strong>\n            <span>Save company name, VAT number, billing email, and invoice address.</span>\n          </div>\n          <a class=\"cta-link\" href=\"invoice-details.html\">Manage</a>\n        </div>\n        <div class=\"setting-row\">\n          <div>\n            <strong>Workspace reset</strong>\n            <span>Clear saved project data and return to a clean workspace state.</span>\n          </div>\n          <a class=\"cta-link\" href=\"dashboard.html\" data-reset-workspace>Reset</a>\n        </div>\n"""
if replace_exact('resources/views/workspace/app/settings.blade.php', settings_old, settings_new):
    changed.append('resources/views/workspace/app/settings.blade.php')

# 6) Prevent invoice page from crashing when no user is logged in.
invoice_old = "value=\"{{ old('billing_email', $invoiceDetail?->billing_email ?? auth()->user()->email) }}\""
invoice_new = "value=\"{{ old('billing_email', $invoiceDetail?->billing_email ?? auth()->user()?->email) }}\""
if replace_exact('resources/views/workspace/app/invoice-details.blade.php', invoice_old, invoice_new):
    changed.append('resources/views/workspace/app/invoice-details.blade.php')

print('CHANGED FILES:')
for item in changed:
    print(item)

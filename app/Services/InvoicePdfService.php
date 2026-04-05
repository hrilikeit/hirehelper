<?php

namespace App\Services;

use App\Models\ClientInvoiceDetail;
use App\Models\Invoice;

class InvoicePdfService
{
    /**
     * Generate an invoice HTML page for print/download.
     */
    public static function generateHtml(Invoice $invoice): string
    {
        $user = $invoice->user;
        $offer = $invoice->offer;
        $invoiceDetail = ClientInvoiceDetail::where('user_id', $invoice->user_id)->first();

        $freelancerName = $offer?->freelancer_display_name ?? 'Freelancer';
        $projectTitle = $invoice->project?->title ?? $offer?->role ?? 'Services';

        $billToName = $invoiceDetail?->contact_name ?: $user?->name ?? '—';
        $billToEmail = $invoiceDetail?->billing_email ?: $user?->email ?? '—';
        $billToCompany = $invoiceDetail?->company_name ?: $user?->company;
        $billToAddress = collect([
            $invoiceDetail?->address_line_1,
            $invoiceDetail?->address_line_2,
            collect([$invoiceDetail?->city, $invoiceDetail?->postal_code])->filter()->implode(' '),
            $invoiceDetail?->country,
        ])->filter()->implode('<br>');
        $vatNumber = $invoiceDetail?->vat_number;

        $periodLabel = '—';
        if ($invoice->period_start && $invoice->period_end) {
            $periodLabel = $invoice->period_start->format('M j, Y') . ' – ' . $invoice->period_end->format('M j, Y');
        }

        $hoursLabel = $invoice->hours ? number_format((float) $invoice->hours, 2) : '—';
        $rateLabel = $invoice->hourly_rate ? '$' . number_format((float) $invoice->hourly_rate, 2) : '—';
        $amountLabel = '$' . number_format((float) $invoice->amount, 2);
        $dateLabel = ($invoice->paid_at ?? $invoice->created_at)->format('F j, Y');

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {$invoice->invoice_number}</title>
<style>
  @page { size: A4; margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1a1c26; font-size: 13px; line-height: 1.5; }
  .invoice { max-width: 800px; margin: 0 auto; padding: 48px 56px; }

  /* Header */
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 24px; border-bottom: 2px solid #6d6af8; }
  .logo-section { display: flex; align-items: center; gap: 12px; }
  .logo-icon { width: 36px; height: 36px; }
  .logo-text { font-size: 22px; font-weight: 700; color: #1a1c26; letter-spacing: -0.02em; }
  .invoice-title { text-align: right; }
  .invoice-title h1 { font-size: 32px; font-weight: 800; color: #6d6af8; letter-spacing: -0.02em; margin-bottom: 2px; }
  .invoice-number { font-size: 14px; color: #6b7280; }

  /* Info grid */
  .info-grid { display: flex; justify-content: space-between; margin-bottom: 36px; }
  .info-block { }
  .info-block.right { text-align: right; }
  .info-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; font-weight: 600; margin-bottom: 6px; }
  .info-value { font-size: 13px; color: #1a1c26; }
  .info-value strong { font-weight: 600; }

  /* Summary box */
  .summary-box { display: flex; justify-content: flex-end; margin-bottom: 36px; }
  .summary-card { background: #f8f7ff; border: 1px solid #e0dffe; border-radius: 8px; padding: 16px 24px; text-align: right; }
  .summary-card .label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #6d6af8; font-weight: 600; }
  .summary-card .amount { font-size: 28px; font-weight: 800; color: #1a1c26; letter-spacing: -0.02em; }

  /* Table */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
  .items-table thead th { background: #6d6af8; color: #fff; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; padding: 12px 16px; text-align: left; }
  .items-table thead th:last-child { text-align: right; }
  .items-table thead th.center { text-align: center; }
  .items-table tbody td { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
  .items-table tbody td:last-child { text-align: right; font-weight: 600; }
  .items-table tbody td.center { text-align: center; }
  .period-note { font-size: 12px; color: #6b7280; padding: 8px 16px; }

  /* Totals */
  .totals { display: flex; justify-content: flex-end; margin-bottom: 40px; }
  .totals-table { width: 260px; }
  .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
  .totals-row.total { border-top: 2px solid #1a1c26; border-bottom: none; padding-top: 12px; font-weight: 700; font-size: 16px; }
  .totals-label { color: #6b7280; }
  .totals-value { font-weight: 600; }
  .totals-row.total .totals-value { color: #6d6af8; }

  /* Footer */
  .footer { border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; }
  .footer-left { font-size: 11px; color: #9ca3af; }
  .footer-right { font-size: 11px; color: #9ca3af; text-align: right; }
  .status-badge { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
  .status-paid { background: #ecfdf5; color: #059669; }
  .status-pending { background: #fffbeb; color: #d97706; }

  @media print {
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .invoice { padding: 40px 48px; }
    .no-print { display: none !important; }
  }
</style>
</head>
<body>
<div class="invoice">
  <!-- Print button (hidden when printing) -->
  <div class="no-print" style="text-align:right;margin-bottom:16px">
    <button onclick="window.print()" style="background:#6d6af8;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer">Download PDF</button>
  </div>

  <!-- Header -->
  <div class="header">
    <div class="logo-section">
      <svg class="logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="18" y="18" width="64" height="64" rx="12" transform="rotate(45 50 50)" stroke="#6d6af8" stroke-width="8" fill="none"/>
        <line x1="35" y1="58" x2="50" y2="43" stroke="#6d6af8" stroke-width="7" stroke-linecap="round"/>
        <line x1="50" y1="58" x2="65" y2="43" stroke="#6d6af8" stroke-width="7" stroke-linecap="round"/>
      </svg>
      <span class="logo-text">HireHelper</span>
    </div>
    <div class="invoice-title">
      <h1>INVOICE</h1>
      <div class="invoice-number">{$invoice->invoice_number}</div>
    </div>
  </div>

  <!-- Info -->
  <div class="info-grid">
    <div class="info-block">
      <div class="info-label">From</div>
      <div class="info-value">
        <strong>HireHelper.ai</strong><br>
        hello@hirehelper.ai
      </div>
    </div>
    <div class="info-block">
      <div class="info-label">Bill To</div>
      <div class="info-value">
        <strong>{$billToName}</strong><br>
        {$billToEmail}
HTML;

        if ($billToCompany) {
            $html .= "<br>{$billToCompany}";
        }
        if ($billToAddress) {
            $html .= "<br>{$billToAddress}";
        }
        if ($vatNumber) {
            $html .= "<br>VAT: {$vatNumber}";
        }

        $statusClass = $invoice->status === 'paid' ? 'status-paid' : 'status-pending';
        $statusLabel = ucfirst($invoice->status);

        $html .= <<<HTML
      </div>
    </div>
    <div class="info-block right">
      <div class="info-label">Date</div>
      <div class="info-value" style="margin-bottom:12px"><strong>{$dateLabel}</strong></div>
      <div class="info-label">Status</div>
      <div class="info-value"><span class="status-badge {$statusClass}">{$statusLabel}</span></div>
    </div>
  </div>

  <!-- Balance due -->
  <div class="summary-box">
    <div class="summary-card">
      <div class="label">Balance Due</div>
      <div class="amount">{$amountLabel}</div>
    </div>
  </div>

  <!-- Items table -->
  <table class="items-table">
    <thead>
      <tr>
        <th>Description</th>
        <th class="center">Hours</th>
        <th class="center">Rate</th>
        <th class="center">Status</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{$projectTitle}<br><span style="color:#6b7280;font-size:12px">{$freelancerName}</span></td>
        <td class="center">{$hoursLabel}</td>
        <td class="center">{$rateLabel}</td>
        <td class="center"><span class="status-badge {$statusClass}">{$statusLabel}</span></td>
        <td>{$amountLabel}</td>
      </tr>
    </tbody>
  </table>
  <div class="period-note">
HTML;

        if ($invoice->period_start && $invoice->period_end) {
            $html .= "Period: {$periodLabel}";
            if ($invoice->hours && $invoice->hourly_rate) {
                $html .= " &mdash; {$hoursLabel} hrs @ \${$invoice->hourly_rate}/hr";
            }
        }
        if ($invoice->type === 'bonus' && $invoice->description) {
            $html .= e($invoice->description);
        }

        $html .= <<<HTML
  </div>

  <!-- Totals -->
  <div class="totals">
    <div class="totals-table">
      <div class="totals-row">
        <span class="totals-label">Subtotal</span>
        <span class="totals-value">{$amountLabel}</span>
      </div>
      <div class="totals-row total">
        <span class="totals-label">Total</span>
        <span class="totals-value">{$amountLabel}</span>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="footer-left">
      <strong>HireHelper.ai</strong> &mdash; Freelancer hiring platform<br>
      Payment via {$invoice->payment_method}
    </div>
    <div class="footer-right">
      Thank you for your business!<br>
      hirehelper.ai
    </div>
  </div>
</div>
</body>
</html>
HTML;

        return $html;
    }
}

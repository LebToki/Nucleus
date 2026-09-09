<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->sectionDashboard('command');
    }

    public function sectionDashboard(string $section)
    {
        $dashboards = [
            'command' => [
                'title' => 'Executive Command',
                'subtitle' => 'Company Overview',
                'metrics' => [
                    ['label' => 'Net Retail Sales (MTD)', 'value' => 'AED 480,000', 'note' => '+14% vs last month', 'icon' => 'solar:wallet-money-bold', 'tone' => 'primary'],
                    ['label' => 'Blended Gross Margin', 'value' => '68.5%', 'note' => 'Healthy luxury spread', 'icon' => 'solar:chart-2-bold', 'tone' => 'success'],
                    ['label' => 'Ready-to-Gift Vault Inventory', 'value' => '248 units', 'note' => 'Low stock: Al-Mutlaq', 'icon' => 'solar:box-minimalistic-bold', 'tone' => 'warning'],
                    ['label' => 'Active White-Glove Dispatches', 'value' => '6 in transit', 'note' => '4 completed today', 'icon' => 'solar:delivery-bold', 'tone' => 'info'],
                ],
                'panels' => [
                    ['title' => 'B2C Boutique Basket', 'tag' => 'B2C', 'items' => [['label' => 'Average Order Value', 'value' => 'AED 4,850'], ['label' => 'Toula vs. Wood Ratio', 'value' => '62% Toula'], ['label' => 'Private Viewing Conversion', 'value' => '38%']]],
                    ['title' => 'B2B Sovereign & Corporate Gifting', 'tag' => 'B2B', 'items' => [['label' => 'Open Pipeline Value', 'value' => 'AED 1.24M'], ['label' => 'Custom Kitting Lead Time', 'value' => '4.2 days'], ['label' => 'Aged Receivables', 'value' => 'AED 86,400 · 3 overdue']]],
                    ['title' => 'Supply & Atelier Health', 'tag' => 'OPERATIONS', 'items' => [['label' => 'Supplier LPO Fulfillment', 'value' => '94% on time'], ['label' => 'Personalization Queue', 'value' => '17 orders'], ['label' => 'VIP Pre-Commitment', 'value' => '72% claimed']]],
                    ['title' => 'Mini-Financial Health', 'tag' => 'OWNER VIEW', 'items' => [['label' => 'OPEX Burn (MTD)', 'value' => 'AED 94,800'], ['label' => 'Net Operating Profit (MTD)', 'value' => 'AED 233,700'], ['label' => 'VAT Position', 'value' => '5% UAE VAT']]],
                ],
            ],
            'financial' => ['title' => 'Financial Command', 'subtitle' => 'Mini-Financials', 'metrics' => [['label' => 'Net Revenue (MTD)', 'value' => 'AED 480,000', 'note' => '+14% month on month', 'icon' => 'solar:wallet-money-bold', 'tone' => 'primary'], ['label' => 'Gross Margin', 'value' => '68.5%', 'note' => 'Against supplier COGS', 'icon' => 'solar:chart-2-bold', 'tone' => 'success'], ['label' => 'OPEX Burn', 'value' => 'AED 94,800', 'note' => 'Within monthly plan', 'icon' => 'solar:bill-list-bold', 'tone' => 'warning'], ['label' => 'Aged AR', 'value' => 'AED 86,400', 'note' => '3 invoices overdue', 'icon' => 'solar:danger-triangle-bold', 'tone' => 'danger']], 'panels' => []],
            'crm' => ['title' => 'Clienteling & Sales', 'subtitle' => 'B2C and B2B Relationship Velocity', 'metrics' => [['label' => 'B2C Average Order Value', 'value' => 'AED 4,850', 'note' => 'Target AED 4,500+', 'icon' => 'solar:user-hand-up-bold', 'tone' => 'primary'], ['label' => 'B2B Open Pipeline', 'value' => 'AED 1.24M', 'note' => 'Pro-forma review', 'icon' => 'solar:case-round-bold', 'tone' => 'info'], ['label' => 'Viewing Conversion', 'value' => '38%', 'note' => 'Private Majlis viewings', 'icon' => 'solar:graph-up-bold', 'tone' => 'success'], ['label' => 'Active VIP Allocations', 'value' => '42', 'note' => 'Across rare batches', 'icon' => 'solar:star-bold', 'tone' => 'warning']], 'panels' => []],
            'communications' => ['title' => 'Communications Engine', 'subtitle' => 'Concierge and Protocol Activity', 'metrics' => [['label' => 'Open Concierge Threads', 'value' => '18', 'note' => '6 awaiting response', 'icon' => 'bi:chat-dots-fill', 'tone' => 'primary'], ['label' => 'Protocol Mail', 'value' => '12', 'note' => 'Unread priority messages', 'icon' => 'mage:email', 'tone' => 'info'], ['label' => 'Today’s Majlis Logs', 'value' => '7', 'note' => '3 converted to orders', 'icon' => 'solar:notes-bold', 'tone' => 'success'], ['label' => 'Urgent Follow-ups', 'value' => '4', 'note' => 'Due before 18:00', 'icon' => 'solar:bell-bing-bold', 'tone' => 'warning']], 'panels' => []],
            'inventory' => ['title' => 'Atelier & Finished Inventory', 'subtitle' => 'Vault Readiness and Personalization', 'metrics' => [['label' => 'Ready-to-Gift Sets', 'value' => '248', 'note' => 'Units sealed and ready', 'icon' => 'solar:box-minimalistic-bold', 'tone' => 'primary'], ['label' => 'Kitting Queue', 'value' => '17', 'note' => '4 beyond target lead time', 'icon' => 'solar:scissors-bold', 'tone' => 'warning'], ['label' => 'VIP Waiting List', 'value' => '86', 'note' => 'Pre-orders for rare batches', 'icon' => 'solar:users-group-rounded-bold', 'tone' => 'info'], ['label' => 'COA Coverage', 'value' => '100%', 'note' => 'Hash verified inventory', 'icon' => 'solar:verified-check-bold', 'tone' => 'success']], 'panels' => []],
            'procurement' => ['title' => 'Upstream Procurement', 'subtitle' => 'Distributor Supply Health', 'metrics' => [['label' => 'Open Supplier LPOs', 'value' => '9', 'note' => 'AED 312,000 committed', 'icon' => 'solar:box-bold', 'tone' => 'primary'], ['label' => 'On-Time Fulfillment', 'value' => '94%', 'note' => 'Intact doorstep receipts', 'icon' => 'solar:checklist-minimalistic-bold', 'tone' => 'success'], ['label' => 'Inbound GRNs', 'value' => '3', 'note' => 'Due this week', 'icon' => 'solar:inbox-in-bold', 'tone' => 'info'], ['label' => '30-Day Replenishment Risk', 'value' => '2 batches', 'note' => 'Below velocity threshold', 'icon' => 'solar:danger-triangle-bold', 'tone' => 'warning']], 'panels' => []],
            'logistics' => ['title' => 'Logistics & Doorstep Fulfillment', 'subtitle' => 'White-Glove Custody and Delivery', 'metrics' => [['label' => 'Active Dispatches', 'value' => '6', 'note' => 'Abu Dhabi and Dubai', 'icon' => 'solar:delivery-bold', 'tone' => 'primary'], ['label' => 'Completed Today', 'value' => '4', 'note' => '100% signed custody', 'icon' => 'solar:check-circle-bold', 'tone' => 'success'], ['label' => 'Courier Shipments', 'value' => '11', 'note' => 'GCC regional tracking', 'icon' => 'solar:plain-2-bold', 'tone' => 'info'], ['label' => 'Showcase Manifests', 'value' => '2', 'note' => 'Offsite custody active', 'icon' => 'solar:case-bold', 'tone' => 'warning']], 'panels' => []],
            'portal' => ['title' => 'Client Sanctuary', 'subtitle' => 'Private Client Self-Service', 'metrics' => [['label' => 'Private Allocations', 'value' => '42', 'note' => 'Eligible collection tiers', 'icon' => 'solar:star-bold', 'tone' => 'primary'], ['label' => 'Open Quotations', 'value' => '8', 'note' => 'Awaiting client review', 'icon' => 'solar:document-text-bold', 'tone' => 'info'], ['label' => 'Orders in Delivery', 'value' => '6', 'note' => 'White-glove tracking', 'icon' => 'solar:delivery-bold', 'tone' => 'success'], ['label' => 'Vault Certificates', 'value' => '126', 'note' => 'Verified PDF records', 'icon' => 'solar:diploma-verified-bold', 'tone' => 'warning']], 'panels' => []],
            'administration' => ['title' => 'System Administration', 'subtitle' => 'Access, Audit, and Boutique Controls', 'metrics' => [['label' => 'Active Team Members', 'value' => '8', 'note' => 'RBAC assignments current', 'icon' => 'solar:users-group-rounded-bold', 'tone' => 'primary'], ['label' => 'Audit Events Today', 'value' => '34', 'note' => 'Price and client activity', 'icon' => 'solar:clipboard-list-bold', 'tone' => 'info'], ['label' => 'VAT Configuration', 'value' => '5%', 'note' => 'UAE tax profile active', 'icon' => 'solar:document-add-bold', 'tone' => 'success'], ['label' => 'System Alerts', 'value' => '2', 'note' => 'Require owner review', 'icon' => 'solar:bell-bing-bold', 'tone' => 'warning']], 'panels' => []],
        ];

        abort_unless(isset($dashboards[$section]), 404);

        return view('dashboard.section', [...$dashboards[$section], 'section' => $section]);
    }

    public function index2()
    {
        return view('dashboard/index2');
    }

    public function index3()
    {
        return view('dashboard/index3');
    }

    public function index4()
    {
        return view('dashboard/index4');
    }

    public function index5()
    {
        return view('dashboard/index5');
    }

    public function index6()
    {
        return view('dashboard/index6');
    }

    public function index7()
    {
        return view('dashboard/index7');
    }

    public function index8()
    {
        return view('dashboard/index8');
    }

    public function index9()
    {
        return view('dashboard/index9');
    }

    public function index10()
    {
        return view('dashboard/index10');
    }
}

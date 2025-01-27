<div class="text-sm ">
    <ul>
        @if ($getRecord()->enable_item_scanning)
            <li>- Item Scanning</li>
        @endif
        @if ($getRecord()->enable_beneficiary_management)
            <li>- Manage Beneficiaries</li>
        @endif
        @if ($getRecord()->enable_list_access)
            <li>- Access Beneficiaries Record</li>
        @endif
    </ul>
</div>

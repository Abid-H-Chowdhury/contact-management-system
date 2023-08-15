<?php

/**
 * @author Sharif Ahmed
 * @copyright 2018
 */
if (isset($_GET["page"]) && !empty($_GET["page"])) {
    $page = $_GET["page"];
    switch ($page) {
        case 'dashboard':
            $Page_Title = 'Dashboard';
            break;
        case 'patient-list':
            $Page_Title = 'Patients List';
            break;
        case 'ipd-patients':
            $Page_Title = 'IPD Patients';
            break;
        case 'result-all':
            $Page_Title = 'All Investigation List';
            break;
        case 'sample-lists':
            $Page_Title = 'Collect Sample';
            break;
        case 'sample-details':
            $Page_Title = 'Sample Details';
            break;
        case 'result-entry':
            $Page_Title = 'Result Entry';
            break;
        case 'result-entry-lists':
            $Page_Title = 'Result Entry';
            break;
        case 'result-lists':
            $Page_Title = 'Result List';
            break;
        case 'result-update':
            $Page_Title = 'Result Update';
            break;
        case 'reagents':
            $Page_Title = 'Reagent Stock';
            break;
        case 'reagent-recharge-history':
            $Page_Title = 'Reagent Recharge History';
            break;
        case 'dashboard-radiology':
            $Page_Title = 'Radiology Dashboard';
            break;
        case 'films':
            $Page_Title = 'Films Stock';
            break;
        case 'films-recharge-history':
            $Page_Title = 'Films Recharge History';
            break;
        case 'films-setup':
            $Page_Title = 'Films Setup';
            break;
        case 'investigations':
            $Page_Title = 'Investigation List';
            break;
        case 'investigations-setup':
            $Page_Title = 'Investigations Setup';
            break;
        case 'investigation-setup-details':
            $Page_Title = 'Investigations Setup Details';
            break;
        case 'investigation-setup-new':
            $Page_Title = 'New Investigations Setup';
            break;
        case 'settings':
            $Page_Title = 'Lab Settings';
            break;
        case 'result-lists-radiology':
            $Page_Title = 'Radiology Result List';
            break;
        case 'my-profile':
            $Page_Title = 'My Profile';
            break;
        case 'bulk-result-manage':
            $Page_Title = 'Bulk Result Management';
            break;
        case 'lab-reg-book':
            $Page_Title = 'Lab Registry Book';
            break;
        case 'thana-list':
            $Page_Title = 'Thana list';
            break;
        case 'union-list':
            $Page_Title = 'Union list';
            break;
        case 'district-list':
            $Page_Title = 'District list';
            break;
        case 'add-contact':
            $Page_Title = 'Add Contact';
            break;
        case 'contact-list':
            $Page_Title = 'Contact List';
            break;
        case 'add-thana':
            $Page_Title = 'Add Thana';
            break;
        case 'add-union':
            $Page_Title = 'Add Union';
            break;
        default:
            $Page_Title = 'Contact Management System';
    }
} else {
    $Page_Title = 'Contact Management System';
}
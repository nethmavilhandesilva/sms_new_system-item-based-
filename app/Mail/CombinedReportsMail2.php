<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CombinedReportsMail2 extends Mailable
{
    use Queueable, SerializesModels;

    public $dayStartReportData;
    public $grnReportData;
    public $salesReportData;
    public $dayStartDate;
    public $weightBasedReportData;
    public $salesByBill;
    public $salesadjustments;
    public $loans; // raw loans
    public $highlightedLoans;
    public $finalLoans; // ✅ enriched loans with highlight

    // Financial report properties
    public $financialReportData;
    public $financialTotalDr;
    public $financialTotalCr;
    public $financialProfit;
    public $financialDamages;
    public $profitTotal;
    public $totalDamages;

    // --- ⬇️ NEW PROPERTIES ADDED HERE ⬇️ ---
    /**
     * The main data for the GRN sales report table.
     * @var \Illuminate\Support\Collection
     */
    public $grnSalesReport;

    /**
     * Today's loan total for the summary card.
     * @var float
     */
    public $grnSales_todayLoanTotal;

    /**
     * Old loan total for the summary card.
     * @var float
     */
    public $grnSales_oldLoanTotal;

    /**
     * Expense categories for the summary card.
     * @var \Illuminate\Support\Collection
     */
    public $grnSales_expenseCategories;
    // --- ⬆️ END OF NEW PROPERTIES ⬆️ ---


    /**
     * Create a new message instance.
     */
    public function __construct(
        $dayStartReportData,
        $grnReportData,
        $salesReportData,
        $dayStartDate,
        $weightBasedReportData,
        $salesByBill,
        $salesadjustments = null,
        $financialReportData = null,
        $financialTotalDr = 0,
        $financialTotalCr = 0,
        $financialProfit = 0,
        $financialDamages = 0,
        $profitTotal = 0,
        $totalDamages = 0,
        $loans = null,
        $highlightedLoans = null,
        $finalLoans = null,
        
        // --- ⬇️ NEW PARAMETERS ADDED HERE ⬇️ ---
        $grnSalesReport = null,
        $grnSales_todayLoanTotal = 0,
        $grnSales_oldLoanTotal = 0,
        $grnSales_expenseCategories = null
        // --- ⬆️ END OF NEW PARAMETERS ⬆️ ---
    ) {
        $this->dayStartReportData = $dayStartReportData;
        $this->grnReportData = $grnReportData;
        $this->salesReportData = $salesReportData;
        $this->dayStartDate = $dayStartDate;
        $this->weightBasedReportData = $weightBasedReportData;
        $this->salesByBill = $salesByBill;
        $this->salesadjustments = $salesadjustments;

        $this->financialReportData = $financialReportData;
        $this->financialTotalDr = $financialTotalDr;
        $this->financialTotalCr = $financialTotalCr;
        $this->financialProfit = $financialProfit;
        $this->financialDamages = $financialDamages;
        $this->profitTotal = $profitTotal;
        $this->totalDamages = $totalDamages;

        $this->loans = $loans;
        $this->highlightedLoans = $highlightedLoans;
        $this->finalLoans = $finalLoans;

        // --- ⬇️ NEW ASSIGNMENTS ADDED HERE ⬇️ ---
        $this->grnSalesReport = $grnSalesReport;
        $this->grnSales_todayLoanTotal = $grnSales_todayLoanTotal;
        $this->grnSales_oldLoanTotal = $grnSales_oldLoanTotal;
        $this->grnSales_expenseCategories = $grnSales_expenseCategories ?? collect(); // Ensure it's a collection
        // --- ⬆️ END OF NEW ASSIGNMENTS ⬆️ ---
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('ඒකාබද්ධ දින වාර්තාව2 - ' . $this->dayStartDate->format('Y-m-d'))
            ->to([
                'nethmavilhan2005@gmail.com',
                'thrcorner@gmail.com',
                'wey.b32@gmail.com',
            ])
            ->view('emails.day_start_report2')
            ->with([
                'dayStartReportData' => $this->dayStartReportData,
                'grnReportData' => $this->grnReportData,
                'salesReportData' => $this->salesReportData,
                'dayStartDate' => $this->dayStartDate,
                'weightBasedReportData' => $this->weightBasedReportData,
                'salesByBill' => $this->salesByBill,
                'salesadjustments' => $this->salesadjustments,
                'financialReportData' => $this->financialReportData,
                'financialTotalDr' => $this->financialTotalDr,
                'financialTotalCr' => $this->financialTotalCr,
                'financialProfit' => $this->financialProfit,
                'financialDamages' => $this->financialDamages,
                'profitTotal' => $this->profitTotal,
                'totalDamages' => $this->totalDamages,
                'loans' => $this->loans,
                'highlightedLoans' => $this->highlightedLoans,
                'finalLoans' => $this->finalLoans,
                
                // --- ⬇️ NEW VARIABLES PASSED TO VIEW ⬇️ ---
                'grnSalesReport' => $this->grnSalesReport,
                'grnSales_todayLoanTotal' => $this->grnSales_todayLoanTotal,
                'grnSales_oldLoanTotal' => $this->grnSales_oldLoanTotal,
                'grnSales_expenseCategories' => $this->grnSales_expenseCategories,
                // --- ⬆️ END OF NEW VARIABLES ⬆️ ---
            ]);
    }
}
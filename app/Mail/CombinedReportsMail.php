<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CombinedReportsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dayStartReportData;
    public $grnReportData;
    public $salesReportData;
    public $dayStartDate;
    public $weightBasedReportData;
    public $final_total; // <-- ADDED: Public property for final_total
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

    /**
     * Create a new message instance.
     */
    public function __construct(
        $dayStartReportData,
        $grnReportData,
        $salesReportData,
        $dayStartDate,
        $weightBasedReportData,
        $final_total, // <-- ADDED: Parameter to accept the named argument
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
        $finalLoans = null  // ✅ Add parameter // ✅ new parameter
    ) {
        $this->dayStartReportData = $dayStartReportData;
        $this->grnReportData = $grnReportData;
        $this->salesReportData = $salesReportData;
        $this->dayStartDate = $dayStartDate;
        $this->weightBasedReportData = $weightBasedReportData;
        $this->final_total = $final_total; // <-- ADDED: Assignment to the property
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
        $this->finalLoans = $finalLoans; // assign enriched loans
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('ඒකාබද්ධ දින වාර්තාව - ' . $this->dayStartDate->format('Y-m-d'))
                    ->to([
                        'nethmavilhan2005@gmail.com',
                        'thrcorner@gmail.com',
                        'wey.b32@gmail.com',
                        
                    ])
                    ->view('emails.day_start_report')
                    ->with([
                        'dayStartReportData' => $this->dayStartReportData,
                        'grnReportData' => $this->grnReportData,
                        'salesReportData' => $this->salesReportData,
                        'dayStartDate' => $this->dayStartDate,
                        'weightBasedReportData' => $this->weightBasedReportData,
                        'final_total' => $this->final_total, // <-- ADDED: Pass to the view
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
                    ]);
    }
}

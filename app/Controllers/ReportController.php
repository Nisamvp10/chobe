<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ReportModel;;
use Mpdf\Mpdf;

class ReportController extends controller
{
    public function index()
    {
        $page = (!haspermission('','report') ? lang('Custom.accessDenied') : 'Repoerts' );
        return view('admin/reports/index',compact('page'));
    }
    function list()
    {   
        $reportModel = new ReportModel();
        $search = $this->request->getGet('search');
        $filer = $this->request->getGet('filer');
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
  
        $reportResult = $reportModel->getReports($search, $filer,$startDate,$endDate);

       // print_r($reportResult);
        
        return $this->response->setJSON(['success' => true , 'result' => $reportResult ]);
       
    }

     public function generateReport()
    {
       $reportModel = new ReportModel();
        $search = $this->request->getGet('search');
        $filer = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
  
        $reportResult = $reportModel->getReports($search, $filer,$startDate,$endDate);
        $data['activities'] = $reportResult;

        // Load HTML view
        $html = view('admin/reports/reportPdftemp', $data);

        // Init mPDF
        $mpdf = new Mpdf();

        // Set header & footer (optional)
        $mpdf->SetHTMLHeader('<h4 style="text-align:center;">Activity Report</h4>');
        $mpdf->SetHTMLFooter('<div style="text-align:center;">Generated on: '.date('d-m-Y H:i:s').'</div>');

        // Write HTML
        $mpdf->WriteHTML($html);

        // Output file (download)
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($mpdf->Output('report-'.date('d-m-Y').'.pdf', 'I'));
    }

}
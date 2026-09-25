<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function export(array $data, string $format): StreamedResponse
    {
        return match ($format) {
            'csv' => $this->csv($data),
            'xlsx' => $this->excel($data),
            'pdf' => $this->pdf($data),
            default => $this->csv($data),
        };
    }

    private function csv(array $data): StreamedResponse
    {
        $filename = 'relatorio-'.now()->format('YmdHis').'.csv';

        $response = new StreamedResponse(function () use ($data): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Campanha', 'Plataforma', 'Investimento', 'Impressões', 'Alcance', 'Cliques', 'CTR', 'CPC', 'CPM', 'Leads', 'Conversões', 'CPL', 'Receita', 'ROAS']);

            foreach ($data['rows'] as $row) {
                fputcsv($handle, [
                    $row['campaign_name'],
                    $row['platform'],
                    number_format($row['spend'], 2, ',', '.'),
                    $row['impressions'],
                    $row['reach'],
                    $row['clicks'],
                    number_format($row['ctr'], 2, ',', '.').'%',
                    number_format($row['cpc'], 2, ',', '.'),
                    number_format($row['cpm'], 2, ',', '.'),
                    $row['leads'],
                    $row['conversions'],
                    number_format($row['cpl'], 2, ',', '.'),
                    number_format($row['revenue'], 2, ',', '.'),
                    number_format($row['roas'], 2, ',', '.').'x',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    private function excel(array $data): StreamedResponse
    {
        $filename = 'relatorio-'.now()->format('YmdHis').'.xlsx';

        return response()->streamDownload(function () use ($data): void {
            echo "Campanha;Plataforma;Investimento;Impressões;Alcance;Cliques;CTR;CPC;CPM;Leads;Conversões;CPL;Receita;ROAS\n";

            foreach ($data['rows'] as $row) {
                echo implode(';', [
                    $row['campaign_name'],
                    $row['platform'],
                    number_format($row['spend'], 2, ',', '.'),
                    $row['impressions'],
                    $row['reach'],
                    $row['clicks'],
                    number_format($row['ctr'], 2, ',', '.').'%',
                    number_format($row['cpc'], 2, ',', '.'),
                    number_format($row['cpm'], 2, ',', '.'),
                    $row['leads'],
                    $row['conversions'],
                    number_format($row['cpl'], 2, ',', '.'),
                    number_format($row['revenue'], 2, ',', '.'),
                    number_format($row['roas'], 2, ',', '.').'x',
                ])."\n";
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function pdf(array $data): StreamedResponse
    {
        $filename = 'relatorio-'.now()->format('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($data): void {
            echo "<html><body><h2>Relatório TrafegoHub</h2><table border='1' cellspacing='0' cellpadding='4'>";
            echo "<tr><th>Campanha</th><th>Investimento</th><th>Cliques</th><th>CTR</th><th>Leads</th><th>ROAS</th></tr>";

            foreach ($data['rows'] as $row) {
                echo '<tr>';
                echo '<td>'.$row['campaign_name'].'</td>';
                echo '<td>R$ '.number_format($row['spend'], 2, ',', '.').'</td>';
                echo '<td>'.$row['clicks'].'</td>';
                echo '<td>'.number_format($row['ctr'], 2, ',', '.').'%</td>';
                echo '<td>'.$row['leads'].'</td>';
                echo '<td>'.number_format($row['roas'], 2, ',', '.').'x</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

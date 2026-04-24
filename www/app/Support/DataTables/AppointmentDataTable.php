<?php

namespace App\Support\DataTables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class AppointmentDataTable extends AbstractDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'appointments.action')
            ->editColumn('scheduled_at', function ($row) {
                return $row->scheduled_at->format('d/m/Y H:i');
            })
            ->editColumn('status', function ($row) {
                $badges = [
                    'scheduled' => '<span class="badge bg-primary">Agendado</span>',
                    'confirmed' => '<span class="badge bg-success">Confirmado</span>',
                    'arrived' => '<span class="badge bg-info">Aguardando</span>',
                    'in_consultation' => '<span class="badge bg-warning text-dark">Em Consulta</span>',
                    'finished' => '<span class="badge bg-secondary">Finalizado</span>',
                    'canceled' => '<span class="badge bg-danger">Cancelado</span>',
                    'no_show' => '<span class="badge bg-dark">Faltou</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    public function query(Appointment $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['client', 'doctor']);
        $request = request();

        // Se for medico restringe forçado
        if (auth()->guard('doctor')->check()) {
            $query->where('doctor_id', auth()->guard('doctor')->id())
                  ->whereDate('scheduled_at', today());
        }

        // Filtros AJAX via DataTables Request Array
        if ($request->filled('date_filter')) {
            $query->whereDate('scheduled_at', $request->date_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('doctor_filter') && !auth()->guard('doctor')->check()) {
            $query->where('doctor_id', $request->doctor_filter);
        }

        if ($request->filled('client_filter')) {
            $query->where('client_id', $request->client_filter);
        }

        // Se o FrontEnd DELETOU o sort natural clicando nas colunas, o DataTables aplicará ele depois via JS.
        // Contudo a nossa query base terá o peso da nossa Métrica Magna (Prioritária) de Data/Status:
        
        $query->orderByRaw("
            CASE 
                WHEN DATE(scheduled_at) = CURDATE() AND status IN ('arrived', 'confirmed') THEN 1
                WHEN DATE(scheduled_at) = CURDATE() AND status = 'in_consultation' THEN 2
                WHEN DATE(scheduled_at) = CURDATE() AND status IN ('finished', 'canceled', 'no_show') THEN 3
                WHEN DATE(scheduled_at) = CURDATE() + INTERVAL 1 DAY THEN 4
                ELSE 5
            END ASC
        ")->orderBy('scheduled_at', 'asc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('appointments-table')
            ->columns($this->getColumns())
            ->ajax([
                'url' => '',
                'data' => 'function(d) {
                    d.date_filter = $("#date_filter").val();
                    d.status_filter = $("#status_filter").val();
                    d.doctor_filter = $("#doctor_filter").val();
                    d.client_filter = $("#client_filter").val();
                }'
            ])
            ->parameters([
                'order' => [], // Remove sort default travado do DataTables, usa orderbyRaw!
                'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'],
                'responsive' => true,
                'pageLength' => 25,
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->hidden(),
            Column::make('scheduled_at')->title('Data/Hora'),
            Column::make('client.name')->title('Paciente'),
            Column::make('doctor.name')->title('Médico'),
            Column::make('consultation_type')->title('Tipo'),
            Column::make('status')->title('Status')->addClass('text-center'),
            Column::computed('action')
                  ->title('Ações')
                  ->exportable(false)
                  ->printable(false)
                  ->width(120)
                  ->addClass('text-center'),
        ];
    }
}

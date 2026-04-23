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
        
        if (auth()->guard('doctor')->check()) {
            $query->where('doctor_id', auth()->guard('doctor')->id())
                  ->whereDate('scheduled_at', today());
        }
        
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('appointments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getBuilderParameters())
            ->orderBy(1);
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

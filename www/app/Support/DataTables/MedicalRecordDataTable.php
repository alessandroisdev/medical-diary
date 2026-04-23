<?php

namespace App\Support\DataTables;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class MedicalRecordDataTable extends AbstractDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($row){
                $safeJson = htmlspecialchars(json_encode([
                    'id' => $row->id,
                    'client_id' => $row->client_id,
                    'doctor_id' => $row->doctor_id,
                    'appointment_id' => $row->appointment_id,
                    'symptoms' => $row->symptoms,
                    'diagnosis' => $row->diagnosis,
                    'treatment_plan' => $row->treatment_plan,
                    'notes' => $row->notes
                ]), ENT_QUOTES, 'UTF-8');
                
                return '
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Editar Prontuário" onclick="editRecord('.$safeJson.')">
                        <i class="bi bi-journal-medical"></i>
                    </button>
                    <a href="/prescriptions/'.$row->id.'/print" target="_blank" class="btn btn-sm btn-outline-secondary" title="Imprimir Receita/Prontuário">
                        <i class="bi bi-printer"></i>
                    </a>
                </div>';
            })
            ->editColumn('symptoms', function ($row) {
                return \Str::limit($row->symptoms, 50);
            })
            ->editColumn('diagnosis', function ($row) {
                return '<span class="fw-semibold text-danger">'.\Str::limit($row->diagnosis, 50).'</span>';
            })
            ->rawColumns(['diagnosis', 'action'])
            ->setRowId('id');
    }

    public function query(MedicalRecord $model): QueryBuilder
    {
        return $model->newQuery()->with(['client', 'doctor']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('medical-records-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getBuilderParameters())
            ->orderBy(1);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->hidden(),
            Column::make('created_at')->title('Data da Avaliação')->render('function(){ return data ? new Date(data).toLocaleDateString("pt-BR") : "-";}'),
            Column::make('client.name')->title('Paciente'),
            Column::make('doctor.name')->title('Médico(a)'),
            Column::make('symptoms')->title('Sintomas Chave'),
            Column::make('diagnosis')->title('Diagnóstico'),
            Column::computed('action')->title('Ações')->exportable(false)->printable(false)->width(100)->addClass('text-center'),
        ];
    }
}

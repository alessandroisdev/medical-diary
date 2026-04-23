<?php

namespace App\Support\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class TransactionDataTable extends AbstractDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'transactions.action')
            ->editColumn('amount', function ($row) {
                return 'R$ ' . number_format($row->amount, 2, ',', '.');
            })
            ->editColumn('type', function ($row) {
                return $row->type === 'income' 
                    ? '<span class="text-success"><i class="bi bi-arrow-down-circle"></i> Receita</span>'
                    : '<span class="text-danger"><i class="bi bi-arrow-up-circle"></i> Despesa</span>';
            })
            ->editColumn('status', function ($row) {
                $badges = [
                    'pending' => '<span class="badge bg-warning text-dark">Pendente</span>',
                    'paid' => '<span class="badge bg-success">Pago</span>',
                    'failed' => '<span class="badge bg-danger">Falhou</span>',
                    'refunded' => '<span class="badge bg-secondary">Reembolsado</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->editColumn('due_date', function($row) {
                return $row->due_date ? $row->due_date->format('d/m/Y') : '-';
            })
            ->rawColumns(['type', 'status'])
            ->setRowId('id');
    }

    public function query(Transaction $model): QueryBuilder
    {
        return $model->newQuery()->with(['client', 'appointment']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getBuilderParameters())
            ->orderBy(1);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->hidden(),
            Column::make('created_at')->title('Data Lançamento')->render('function(){ return data ? new Date(data).toLocaleDateString("pt-BR") : "-";}'),
            Column::make('client.name')->title('Cliente/Paciente'),
            Column::make('amount')->title('Valor')->addClass('text-end fw-bold'),
            Column::make('type')->title('Tipo')->addClass('text-center'),
            Column::make('payment_method')->title('Método'),
            Column::make('status')->title('Status')->addClass('text-center'),
            Column::make('due_date')->title('Vencimento')->addClass('text-center'),
            Column::computed('action')
                  ->title('Ações')
                  ->exportable(false)
                  ->printable(false)
                  ->width(80)
                  ->addClass('text-center text-nowrap'),
        ];
    }
}

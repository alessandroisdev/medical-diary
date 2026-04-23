<?php

namespace App\Support\DataTables;

use Yajra\DataTables\Services\DataTable;

abstract class AbstractDataTable extends DataTable
{
    /**
     * Define o método PADRÃO como POST para as Datatables Server-Side.
     * Necessário como regra de negócios do sistema.
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Construção genérica dos parâmetros padrões da Datatable
     *
     * @return array
     */
    protected function getBuilderParameters(): array
    {
        return [
            'dom'          => 'Bfrtip',
            'order'        => [[0, 'desc']],
            'buttons'      => ['excel', 'csv', 'print', 'reset', 'reload'],
            'language'     => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            ],
            'responsive'   => true,
            'autoWidth'    => false,
            'processing'   => true,
            'serverSide'   => true,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "11.0.0",
    description: "API Central do sistema administrativo (Modernizado para Recursos Full-Page anti-modais, com Motor SSE de Totem).",
    title: "Medical Diary SaaS API",
)]
#[OA\Server(
    url: "http://localhost:8084",
    description: "Local API Server"
)]
abstract class Controller
{
    //
}

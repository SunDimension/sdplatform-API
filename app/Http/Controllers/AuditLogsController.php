<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogsStoreRequest;
use App\Http\Requests\AuditLogsUpdateRequest;
use App\Http\Resources\AuditLogCollection;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuditLogsController extends Controller
{
    public function index(Request $request): AuditLogCollection
    {
        $auditLogs = AuditLog::all();

        return new AuditLogCollection($auditLogs);
    }

    public function store(AuditLogsStoreRequest $request): AuditLogResource
    {
        $auditLog = AuditLog::create($request->validated());

        return new AuditLogResource($auditLog);
    }

    public function show(Request $request, AuditLog $auditLog): AuditLogResource
    {
        return new AuditLogResource($auditLog);
    }

    public function update(AuditLogsUpdateRequest $request, AuditLog $auditLog): AuditLogResource
    {
        $auditLog->update($request->validated());

        return new AuditLogResource($auditLog);
    }

    public function destroy(Request $request, AuditLog $auditLog): Response
    {
        $auditLog->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogHistory;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LogHistoryReportController extends Controller
{
    public function index()
    {
        $users = User::all();
        $tables = LogHistory::distinct()->pluck('table_name');
        $actions = ['create', 'update', 'delete'];
        
        return view('dashboard.reports.log-histories', compact('users', 'tables', 'actions'));
    }

    public function data(Request $request)
    {
        $query = LogHistory::leftJoin('users', 'users.id', '=', 'log_histories.user')
            ->select(
                'log_histories.id',
                'log_histories.table_name',
                'log_histories.entity_id',
                'log_histories.action',
                'log_histories.user as user_id',
                'log_histories.old_data',
                'log_histories.new_data',
                'log_histories.timestamp',
                'users.name as user_name'
            );

        if (!empty($request->date_from)) {
            $query->whereDate('log_histories.timestamp', '>=', Carbon::parse($request->date_from));
        }
        if (!empty($request->date_to)) {
            $query->whereDate('log_histories.timestamp', '<=', Carbon::parse($request->date_to));
        }
        if (!empty($request->user_id)) {
            $query->where('log_histories.user', $request->user_id);
        }
        if (!empty($request->table_name)) {
            $query->where('log_histories.table_name', $request->table_name);
        }
        if (!empty($request->action)) {
            $query->where('log_histories.action', $request->action);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('timestamp', function ($log) {
                return Carbon::parse($log->timestamp)->format('d-m-Y H:i:s');
            })
            ->editColumn('user_name', function ($log) {
                return $log->user_name ?: 'Guest';
            })
            ->editColumn('action', function ($log) {
                switch ($log->action) {
                    case 'create':
                        return '<span class="badge bg-success">Create</span>';
                    case 'update':
                        return '<span class="badge bg-primary">Update</span>';
                    case 'delete':
                        return '<span class="badge bg-danger">Delete</span>';
                    default:
                        return '<span class="badge bg-secondary">' . ucfirst($log->action) . '</span>';
                }
            })
            ->addColumn('details', function ($log) {
                $diffCount = 0;
                
                if ($log->action === 'create') {
                    $diffCount = is_array($log->new_data) ? count($log->new_data) : 0;
                } elseif ($log->action === 'update') {
                    $oldData = is_array($log->old_data) ? $log->old_data : [];
                    $newData = is_array($log->new_data) ? $log->new_data : [];
                    
                    foreach ($oldData as $key => $value) {
                        if (isset($newData[$key]) && $newData[$key] != $value) {
                            $diffCount++;
                        }
                    }
                } elseif ($log->action === 'delete') {
                    $diffCount = is_array($log->old_data) ? count($log->old_data) : 0;
                }
                
                return $diffCount > 0 ? $diffCount . ' field(s)' : 'No changes';
            })
            ->addColumn('action_btn', function ($log) {
                return '<button class="btn btn-sm btn-info view-log-btn" data-id="'.$log->id.'">
                            <i class="fas fa-eye"></i>
                        </button>';
            })
            ->rawColumns(['action', 'action_btn'])
            ->make(true);
    }

    public function show($id)
    {
        $log = LogHistory::leftJoin('users', 'users.id', '=', 'log_histories.user')
            ->select(
                'log_histories.*',
                'users.name as user_name'
            )
            ->findOrFail($id);

        $data = [
            'id' => $log->id,
            'table_name' => $log->table_name,
            'entity_id' => $log->entity_id,
            'action' => $log->action,
            'action_html' => '',
            'timestamp' => Carbon::parse($log->timestamp)->format('d-m-Y H:i:s'),
            'user_name' => $log->user_name ?: 'Guest',
            'old_data' => $log->old_data,
            'new_data' => $log->new_data,
            'changes' => []
        ];

        switch ($log->action) {
            case 'create':
                $data['action_html'] = '<span class="badge bg-success">Create</span>';
                break;
            case 'update':
                $data['action_html'] = '<span class="badge bg-primary">Update</span>';
                break;
            case 'delete':
                $data['action_html'] = '<span class="badge bg-danger">Delete</span>';
                break;
            default:
                $data['action_html'] = '<span class="badge bg-secondary">' . ucfirst($log->action) . '</span>';
        }

        if ($log->action === 'create') {
            if (is_array($log->new_data)) {
                foreach ($log->new_data as $key => $value) {
                    $data['changes'][] = [
                        'field' => $key,
                        'old_value' => '-',
                        'new_value' => is_array($value) || is_object($value) 
                            ? json_encode($value, JSON_PRETTY_PRINT) 
                            : $value
                    ];
                }
            }
        } elseif ($log->action === 'update') {
            $oldData = is_array($log->old_data) ? $log->old_data : [];
            $newData = is_array($log->new_data) ? $log->new_data : [];
            
            foreach ($oldData as $key => $value) {
                if (isset($newData[$key]) && $newData[$key] != $value) {
                    $data['changes'][] = [
                        'field' => $key,
                        'old_value' => is_array($value) || is_object($value) 
                            ? json_encode($value, JSON_PRETTY_PRINT) 
                            : $value,
                        'new_value' => is_array($newData[$key]) || is_object($newData[$key]) 
                            ? json_encode($newData[$key], JSON_PRETTY_PRINT) 
                            : $newData[$key]
                    ];
                }
            }
        } elseif ($log->action === 'delete') {
            if (is_array($log->old_data)) {
                foreach ($log->old_data as $key => $value) {
                    $data['changes'][] = [
                        'field' => $key,
                        'old_value' => is_array($value) || is_object($value) 
                            ? json_encode($value, JSON_PRETTY_PRINT) 
                            : $value,
                        'new_value' => '-'
                    ];
                }
            }
        }

        return response()->json($data);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private function recentActivityLogs(): Collection
    {
        if (! Schema::hasTable('activity_logs')) {
            return collect();
        }

        return ActivityLog::with('user')->latest()->take(6)->get();
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'bookCount' => Book::count(),
                'memberCount' => User::where('role', 'member')->count(),
                'pendingLoanCount' => Loan::where('status', 'pending')->count(),
                'rejectedLoanCount' => Loan::where('status', 'rejected')->count(),
                'activeLoanCount' => Loan::whereIn('status', ['borrowed', 'late'])->count(),
                'returnedLoanCount' => Loan::where('status', 'returned')->count(),
                'collectedFineTotal' => Loan::sum('fine_amount'),
                'recentLoans' => Loan::with(['user', 'book'])->latest()->take(5)->get(),
                'recentActivityLogs' => $this->recentActivityLogs(),
            ]);
        }

        return view('dashboard.member', [
            'availableBooks' => Book::where('stock_available', '>', 0)->count(),
            'pendingLoanCount' => Loan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'rejectedLoanCount' => Loan::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'activeLoanCount' => Loan::where('user_id', $user->id)->whereIn('status', ['borrowed', 'late'])->count(),
            'returnedLoanCount' => Loan::where('user_id', $user->id)->where('status', 'returned')->count(),
            'fineTotal' => Loan::where('user_id', $user->id)->sum('fine_amount'),
            'memberLoans' => Loan::with('book')->where('user_id', $user->id)->latest()->take(5)->get(),
        ]);
    }
}

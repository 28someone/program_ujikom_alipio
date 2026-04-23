<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Masuk ke sistem perpustakaan');
    }

    public function test_login_requires_exact_username_case(): void
    {
        $user = User::factory()->create([
            'username' => 'AdminCase',
            'password' => 'secret123',
        ]);

        $wrongCaseResponse = $this->post('/login', [
            'username' => 'admincase',
            'password' => 'secret123',
        ]);

        $wrongCaseResponse->assertSessionHasErrors('username');
        $this->assertGuest();

        $correctCaseResponse = $this->post('/login', [
            'username' => 'AdminCase',
            'password' => 'secret123',
        ]);

        $correctCaseResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_member_can_borrow_book_and_stock_is_reduced(): void
    {
        $member = User::factory()->create();
        $category = Category::create([
            'name' => 'Pemrograman',
            'slug' => 'pemrograman',
        ]);

        $book = Book::create([
            'category_id' => $category->id,
            'code' => 'BK-TEST',
            'title' => 'Laravel Dasar',
            'author' => 'Penguji',
            'stock_total' => 2,
            'stock_available' => 2,
        ]);

        $response = $this->actingAs($member)->post('/peminjaman', [
            'book_id' => $book->id,
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertRedirect(route('loans.index'));
        $this->assertDatabaseHas('loans', [
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'borrowed',
        ]);
        $this->assertSame(1, $book->fresh()->stock_available);
    }

    public function test_returning_book_restores_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();
        $category = Category::create([
            'name' => 'Novel',
            'slug' => 'novel',
        ]);

        $book = Book::create([
            'category_id' => $category->id,
            'code' => 'BK-RETURN',
            'title' => 'Buku Kembali',
            'author' => 'Penguji',
            'stock_total' => 3,
            'stock_available' => 2,
        ]);

        $loan = Loan::create([
            'loan_code' => 'TRX-0099',
            'user_id' => $member->id,
            'book_id' => $book->id,
            'processed_by' => $admin->id,
            'borrowed_at' => now()->subDay()->toDateString(),
            'due_at' => now()->addDays(6)->toDateString(),
            'status' => 'borrowed',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($member)->post(route('loans.return', $loan));

        $response->assertRedirect(route('loans.index'));
        $this->assertSame('returned', $loan->fresh()->status);
        $this->assertSame(3, $book->fresh()->stock_available);
    }

    public function test_late_return_generates_fine(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();
        $category = Category::create([
            'name' => 'Referensi',
            'slug' => 'referensi',
        ]);

        $book = Book::create([
            'category_id' => $category->id,
            'code' => 'BK-FINE',
            'title' => 'Buku Denda',
            'author' => 'Penguji',
            'stock_total' => 2,
            'stock_available' => 1,
        ]);

        $loan = Loan::create([
            'loan_code' => 'TRX-0100',
            'user_id' => $member->id,
            'book_id' => $book->id,
            'processed_by' => $admin->id,
            'borrowed_at' => now()->subDays(10)->toDateString(),
            'due_at' => now()->subDays(3)->toDateString(),
            'status' => 'late',
            'quantity' => 1,
            'fine_amount' => 0,
        ]);

        $this->actingAs($member)->post(route('loans.return', $loan))
            ->assertRedirect(route('loans.index'));

        $this->assertSame(3 * config('library.fine_per_day'), $loan->fresh()->fine_amount);
    }
}

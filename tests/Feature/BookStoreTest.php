<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('stores a book even when the cover column is missing in the database', function () {
    Schema::table('books', function (Blueprint $table) {
        $table->dropColumn('cover');
    });

    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'pending_status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->post(route('books.store'), [
        'title' => 'Laskar Pelangi',
        'author' => 'Andrea Hirata',
        'publisher' => 'Bentang Pustaka',
        'year' => 2005,
        'stock' => 5,
        'genre' => 'Fiksi / Novel',
    ]);

    $response->assertRedirect(route('books.index'));
    $response->assertSessionHas('success', 'Buku berhasil ditambahkan!');

    expect(Book::where('title', 'Laskar Pelangi')->exists())->toBeTrue();
});

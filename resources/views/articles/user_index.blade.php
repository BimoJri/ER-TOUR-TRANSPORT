@extends('layouts.myapp')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold text-dark display-4">Latest Articles</h1>

    <!-- Articles List -->
    <div class="row">
        @foreach ($articles as $article)
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm article-card d-flex flex-row align-items-center p-3">
                    <!-- Image Section -->
                    @if ($article->image)
                        <div class="me-3" style="width: 200px; height: 120px;">
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                    @else
                        <div class="me-3" style="width: 200px; height: 120px;">
                            <img src="{{ asset('images/default-article.jpg') }}" alt="Default Image" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                    @endif

                    <!-- Content Section -->
                    <div class="flex-grow-1">
                        <div class="text-uppercase fw-bold text-danger small mb-1">{{ $article->category ?? 'News' }} | {{ $article->created_at->diffForHumans() }}</div>
                        <h5 class="fw-bold mb-2">{{ $article->title }}</h5>
                        <p class="text-muted mb-2" style="font-size: 0.9rem;">
                            {{ Str::limit($article->content, 150, '...') }}
                        </p>
                        <a href="{{ route('articles.show', $article->id) }}" class="text-decoration-none text-primary fw-bold">Read More</a>
                    </div>
                </div>
                <hr class="my-2">
            </div>
        @endforeach
    </div>
</div>

<!-- Custom Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; 2024 Bimojri.dev. All Rights Reserved.</p>
    </div>
</footer>

<!-- CSS Styling -->
<style>
    .article-card:hover {
        background-color: #f9f9f9;
        transition: background-color 0.3s ease;
    }

    h5 {
        font-size: 1.2rem;
        line-height: 1.4;
    }
</style>
@endsection

@extends('layouts.myapp')

@section('content')
<div class="container">
    <h1 class="text-center">Articles</h1>
    <a href="{{ route('articles.create') }}" class="btn btn-warning mb-3">+ Add New Article</a>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
                <tr>
                    <td>{{ $article->title }}</td>
                    <td>{{ Str::limit($article->content, 50) }}</td>
                    <td>
                        @if ($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="Image" width="100">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

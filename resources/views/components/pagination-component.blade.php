@props(['pagination', 'routeName', 'queryParams' => []])

<div class="d-flex justify-content-between align-items-center my-4">
    <!-- Info Pagination -->
    <div class="text-muted small">
        @if ($pagination['totalItems'] > 0)
            Menampilkan <strong>{{ $pagination['firstItem'] }}-{{ $pagination['lastItem'] }}</strong>
            dari <strong>{{ $pagination['totalItems'] }}</strong> data
            | Halaman <strong>{{ $pagination['currentPage'] }}</strong> dari <strong>{{ $pagination['totalPages'] }}</strong>
        @else
            Tidak ada data
        @endif
    </div>

    <!-- Pagination Buttons -->
    @if ($pagination['totalPages'] > 1)
        <nav aria-label="Pagination">
            <ul class="pagination mb-0">
                <!-- Previous Button -->
                <li class="page-item {{ !$pagination['hasPreviousPage'] ? 'disabled' : '' }}">
                    @if ($pagination['hasPreviousPage'])
                        <a class="page-link" href="{{ route($routeName, array_merge($queryParams, ['page' => 1])) }}" aria-label="First">
                            <span aria-hidden="true">&laquo;</span> First
                        </a>
                    @else
                        <span class="page-link">&laquo; First</span>
                    @endif
                </li>

                <li class="page-item {{ !$pagination['hasPreviousPage'] ? 'disabled' : '' }}">
                    @if ($pagination['hasPreviousPage'])
                        <a class="page-link" href="{{ route($routeName, array_merge($queryParams, ['page' => $pagination['previousPage']])) }}" aria-label="Previous">
                            <span aria-hidden="true">&lsaquo;</span> Prev
                        </a>
                    @else
                        <span class="page-link">&lsaquo; Prev</span>
                    @endif
                </li>

                <!-- Page Numbers -->
                @foreach ($pagination['pageRange'] as $pageNum)
                    <li class="page-item {{ $pageNum === $pagination['currentPage'] ? 'active' : '' }}">
                        @if ($pageNum === $pagination['currentPage'])
                            <span class="page-link">
                                {{ $pageNum }}
                                <span class="visually-hidden">(current)</span>
                            </span>
                        @else
                            <a class="page-link" href="{{ route($routeName, array_merge($queryParams, ['page' => $pageNum])) }}">
                                {{ $pageNum }}
                            </a>
                        @endif
                    </li>
                @endforeach

                <!-- Next Button -->
                <li class="page-item {{ !$pagination['hasNextPage'] ? 'disabled' : '' }}">
                    @if ($pagination['hasNextPage'])
                        <a class="page-link" href="{{ route($routeName, array_merge($queryParams, ['page' => $pagination['nextPage']])) }}" aria-label="Next">
                            Next <span aria-hidden="true">&rsaquo;</span>
                        </a>
                    @else
                        <span class="page-link">Next &rsaquo;</span>
                    @endif
                </li>

                <li class="page-item {{ !$pagination['hasNextPage'] ? 'disabled' : '' }}">
                    @if ($pagination['hasNextPage'])
                        <a class="page-link" href="{{ route($routeName, array_merge($queryParams, ['page' => $pagination['totalPages']])) }}" aria-label="Last">
                            Last <span aria-hidden="true">&raquo;</span>
                        </a>
                    @else
                        <span class="page-link">Last &raquo;</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>

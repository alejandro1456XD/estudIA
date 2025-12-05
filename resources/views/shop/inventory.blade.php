@extends('layouts.app')

@section('title', 'Mi Inventario')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-backpack text-primary me-2"></i>Mi Mochila</h2>
            <p class="text-muted">Gestiona los objetos de tu mascota.</p>
        </div>
        <div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-store me-1"></i> Ir a la Tienda
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-pill mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @forelse($myInventory as $inventoryItem)
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm {{ $inventoryItem->is_equipped ? 'border border-2 border-primary bg-primary-subtle' : '' }}">
                    <div class="card-body text-center p-4">
                        
                        <!-- Icono del Item -->
                        <div class="mb-3">
                            <div class="bg-white rounded-circle p-3 shadow-sm mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                @php $cat = $inventoryItem->item->category; @endphp
                                @if($cat == 'food') <i class="fas fa-cookie-bite fa-2x text-warning"></i>
                                @elseif($cat == 'hat') <i class="fas fa-hat-cowboy fa-2x text-primary"></i>
                                @elseif($cat == 'glasses') <i class="fas fa-glasses fa-2x text-dark"></i>
                                @elseif($cat == 'body') <i class="fas fa-tshirt fa-2x text-info"></i>
                                @else <i class="fas fa-gift fa-2x text-success"></i> @endif
                            </div>
                        </div>

                        <h6 class="fw-bold">{{ $inventoryItem->item->name }}</h6>
                        
                        @if($inventoryItem->is_equipped)
                            <span class="badge bg-primary mb-3">Equipado</span>
                        @else
                            <span class="badge bg-light text-muted mb-3">En mochila</span>
                        @endif

                        <form action="{{ route('shop.equip', $inventoryItem->id) }}" method="POST">
                            @csrf
                            @if($inventoryItem->is_equipped)
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="fas fa-minus-circle me-1"></i> Quitar
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-check-circle me-1"></i> Usar
                                </button>
                            @endif
                        </form>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted opacity-25 mb-3"></i>
                <h4 class="text-muted">Tu mochila está vacía</h4>
                <a href="{{ route('shop.index') }}" class="btn btn-primary mt-3">Ir a Comprar</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
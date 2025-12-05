@extends('layouts.app')

@section('title', 'Tienda de Decoración')

@section('content')
<div class="container">
    <!-- ENCABEZADO -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <!-- Título actualizado -->
            <h2 class="fw-bold"><i class="fas fa-shapes text-primary me-2"></i>Tienda de Decoración</h2>
            <p class="text-muted mb-0">Personaliza el espacio de tu mascota con stickers y accesorios.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <!-- BOTONES DE ACCIÓN -->
            <div class="d-inline-flex align-items-center gap-2">
                <!-- Botón actualizado a "Mis Stickers" -->
                <a href="{{ route('shop.inventory') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-box-open me-2"></i>Mis Stickers
                </a>
                
                <!-- Contador de Monedas -->
                <div class="badge bg-warning text-dark p-2 px-3 fs-6 rounded-pill shadow-sm border border-warning">
                    <i class="fas fa-coins me-2"></i> {{ Auth::user()->coins }}
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-pill mb-4 border-0 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-pill mb-4 border-0 shadow-sm"><i class="fas fa-times-circle me-2"></i>{{ session('error') }}</div>
    @endif

    <!-- LISTA DE STICKERS -->
    <div class="row g-4">
        @forelse($items as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm hover-sticker">
                    <div class="card-body text-center p-4">
                        <!-- Sticker Visual -->
                        <div class="mb-3 position-relative d-inline-block">
                            <!-- Fondo blanco y borde para simular un Sticker de papel -->
                            <div class="bg-white rounded-circle p-3 shadow-sm border" style="width: 90px; height: 90px; display: flex; align-items: center; justify-content: center;">
                                @if($item->category == 'food') <i class="fas fa-cookie-bite fa-3x text-warning"></i>
                                @elseif($item->category == 'hat') <i class="fas fa-hat-wizard fa-3x text-primary"></i>
                                @elseif($item->category == 'glasses') <i class="fas fa-glasses fa-3x text-dark"></i>
                                @elseif($item->category == 'body') <i class="fas fa-shield-alt fa-3x text-info"></i>
                                @else <i class="fas fa-star fa-3x text-success"></i> @endif
                            </div>
                            
                            <!-- Check si ya lo tiene -->
                            @if(in_array($item->id, $myItems))
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-white">
                                    <i class="fas fa-check"></i>
                                </span>
                            @endif
                        </div>

                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $item->name }}">{{ $item->name }}</h6>
                        <span class="badge bg-light text-muted mb-3 border">Sticker</span>

                        <div class="d-grid">
                            @if(in_array($item->id, $myItems))
                                <a href="{{ route('shop.inventory') }}" class="btn btn-sm btn-light border text-muted disabled">
                                    En Inventario
                                </a>
                            @else
                                <form action="{{ route('shop.buy', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill group-hover-btn">
                                        Comprar <span class="fw-bold text-dark ms-1">{{ $item->price }} <i class="fas fa-coins text-warning"></i></span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50 mb-3"><i class="fas fa-store-slash fa-4x"></i></div>
                <h5 class="fw-bold text-muted">Tienda Vacía</h5>
                <p class="text-muted">No hay stickers disponibles por ahora.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Animación de "Pop" al pasar el mouse, más acorde a stickers */
    .hover-sticker { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .hover-sticker:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.1)!important; }
</style>
@endsection
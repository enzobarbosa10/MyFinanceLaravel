@extends('layouts.landing')

@section('content')
    @include('components.landing.hero')
    @include('components.landing.problema')
    @include('components.landing.solucao')
    @include('components.landing.beneficios')
    @include('components.landing.funcionalidades')
    @include('components.landing.depoimentos')
    @include('components.landing.cta-final')
@endsection

@push('scripts')
<script>
    // Mobile menu toggle
    const toggle = document.getElementById('menuToggle');
    const menu = document.getElementById('mobileMenu');
    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        const bar1 = document.getElementById('bar1');
        const bar2 = document.getElementById('bar2');
        const bar3 = document.getElementById('bar3');
        bar1.classList.toggle('translate-y-[8px]');
        bar1.classList.toggle('rotate-45');
        bar2.classList.toggle('opacity-0');
        bar3.classList.toggle('-translate-y-[8px]');
        bar3.classList.toggle('-rotate-45');
    });

    // Close mobile menu on link click
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menu.classList.add('hidden');
        });
    });

    // Navbar shadow on scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('shadow-md', window.scrollY > 20);
    });

    // Simple scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-8');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('section > div').forEach(el => {
        el.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-700', 'ease-out');
        observer.observe(el);
    });
</script>
@endpush

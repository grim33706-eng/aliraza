<x-layouts.app title="Page not found" page="error" description="The page you were looking for could not be found.">
    <section class="error-page">
        <div class="hero-glow" aria-hidden="true"></div>
        <div class="container">
            <p class="error-page__code" aria-hidden="true">404</p>
            <h1 class="page-hero__title">This page didn't convert.</h1>
            <p class="page-hero__lead">The link may be broken or the page may have moved. Let's get you back on track.</p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn--primary btn--lg"><x-icon name="home" /> Back to home</a>
                <a href="{{ route('contact') }}" class="btn btn--ghost btn--lg">Contact me</a>
            </div>
        </div>
    </section>
</x-layouts.app>

<x-app>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">Masuk</div>

          <div class="card-body px-0">
            <form method="POST" action="{{ route('login') }}">
              @csrf
              <div class="form-group row m-3">
                <label for="email" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">
                  Email
                </label>

                <div class="col-lg-7">
                  <input
                    id="email"
                    type="email"
                    class="form-control @error('email') is-invalid @enderror @error('verify') is-invalid @enderror"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                  />

                  @error('verify')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row m-3">
                <label for="password" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">
                  Kata Sandi
                </label>

                <div class="col-lg-7">
                  <input
                    id="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    required
                    autocomplete="current-password"
                  />

                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row m-3">
                <div class="col-lg-7 offset-lg-4">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                      Masuk
                    </button>
                  </div>
                </div>
              </div>

              <div class="form-group row m-3">
                <div class="col-lg-7 offset-lg-4">
                  <div class="d-flex justify-content-between">
                    <a class="btn btn-link p-0" href="{{ route('register') }}">
                      Daftar
                    </a>
                    <a class="btn btn-link p-0" href="{{ route('password.request') }}">
                      Lupa Kata Sandi
                    </a>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app>
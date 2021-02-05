<x-app>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">Atur Ulang Kata Sandi</div>

          <div class="card-body px-0">
            @if (session('status'))
              <div class="alert alert-success" role="alert">
                {{ session('status') }}
              </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
              @csrf

              <div class="form-group row m-3">
                <label for="email" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">
                  Email
                </label>

                <div class="col-lg-7">
                  <input
                    id="email"
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                  />

                  @error('email')
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
                      Kirim Link
                    </button>
                  </div>
                </div>
              </div>

              <div class="form-group row m-3">
                <div class="col-lg-7 offset-lg-4">
                  <div class="d-flex justify-content-between">
                    <a class="btn btn-link p-0" href="{{ route('login') }}">
                      Masuk
                    </a>
                    <a class="btn btn-link p-0" href="{{ route('register') }}">
                      Daftar
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
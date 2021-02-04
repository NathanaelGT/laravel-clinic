<x-app>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">Daftar</div>

          <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
              @csrf

              <div class="form-group row m-3">
                <label for="name" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">Nama</label>

                <div class="col-lg-7">
                  <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                  @error('name')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row m-3">
                <label for="email" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">
                  Email
                </label>

                <div class="col-lg-7">
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

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
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row m-3">
                <label for="password-confirm" class="col-lg-3 offset-lg-1 col-form-label text-lg-right">
                  Ulangi Kata Sandi
                </label>

                <div class="col-lg-7">
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
              </div>

              <div class="form-group row m-3">
                <div class="col-lg-7 offset-lg-4">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                      Daftar
                    </button>
                  </div>

                  <div class="d-flex justify-content-between mt-4">
                    <a class="btn btn-link p-0" href="{{ route('login') }}">
                      Masuk
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
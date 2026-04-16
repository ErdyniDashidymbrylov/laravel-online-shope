<div class="mb-3">
    <label for="name" class="form-label">Имя</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $user?->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        required
    >
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input
        type="email"
        id="email"
        name="email"
        value="{{ old('email', $user?->email ?? '') }}"
        class="form-control @error('email') is-invalid @enderror"
        required
    >
    @error('email')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="role" class="form-label">Роль</label>
    @php($selectedRole = old('role', $user?->roles?->first()?->slug))
    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
        <option value="">Выберите роль</option>
        @foreach($roles as $role)
            <option value="{{ $role->slug }}" @selected($selectedRole === $role->slug)>{{ $role->name }}</option>
        @endforeach
    </select>
    @error('role')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(!$user)
    <div class="mb-3">
        <label for="password" class="form-label">Пароль</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            required
        >
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control"
            required
        >
    </div>
@endif

export default ({
  placeholder, name, type = 'text', oninput = null, value = '',
  currentIndex = -1, onkeypress = null, errorMessage = null, list = null
}) => {
  const input = (
    <input
      id={name}
      name={name}
      type={type}
      placeholder={`Masukkan ${placeholder.toLowerCase()}`}
      ariaLabel={placeholder}
      oninput={oninput}
      onkeypress={onkeypress}
      value={value}
      className="form-control"
      autocomplete="off"
    />
  )
  if (list) input.setAttribute('list', list)

  return (
    <div>
      <label htmlFor={name}>{placeholder}</label>
      {currentIndex !== -1 && <label className="float-end">Halaman {currentIndex + 1}</label>}
      {input}

      <div className={`form-text text-danger ${!errorMessage && 'input-valid-text'}`}>
        {errorMessage || '\xA0'}
      </div>
    </div>
  )
}
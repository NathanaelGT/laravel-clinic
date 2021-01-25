const validateNumber = event => {
  if (event.which !== 8 && event.which !== 0 && event.which < 48 || event.which > 57)
    event.preventDefault()
}

export default ({
  name, leftPlaceholder, leftOninput, leftValue, separator, middlePlaceholder, middleOninput,
  middleValue, rightPlaceholder, rightOninput, rightValue, options, errorMessage = null
}) => (
  <div>
    <label htmlFor={name}>{leftPlaceholder}</label>
    <div className="input-group">
      <input
        type="number"
        min={1}
        className="form-control"
        id={name}
        placeholder={`Masukkan ${leftPlaceholder.toLowerCase()}`}
        ariaLabel={leftPlaceholder}
        onkeypress={validateNumber}
        oninput={leftOninput}
        value={leftValue}
      />
      <span className="input-group-text">{separator}</span>
      <input
        type="number"
        min={1}
        className="form-control"
        placeholder={middlePlaceholder}
        ariaLabel={middlePlaceholder}
        onkeypress={validateNumber}
        oninput={middleOninput}
        value={middleValue}
      />
      <select className="form-select" oninput={rightOninput}>
        <option
          selected={rightValue === undefined}
          hidden
          disabled
          ariaHidden
        >
          Pilih {rightPlaceholder.toLowerCase()}
        </option>
        {options.map(value => (
          <option selected={value === rightValue} value={value}>{value}</option>
        ))}
      </select>

    </div>
    <div className={`form-text text-danger ${!errorMessage && 'input-valid-text'}`}>
      {errorMessage || '\xA0'}
    </div>
  </div>
)
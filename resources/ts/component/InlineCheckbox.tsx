interface Props {
  value: string
  onchange: (event: Event) => void
  checked: boolean
}

export default ({ value, onchange, checked }: Props) => (
  <div className="form-check form-check-inline">
    <input
      className="form-check-input"
      type="checkbox"
      id={value}
      value={value}
      checked={checked}
      onchange={onchange}
    />
    <label className="form-check-label" htmlFor={value}>{value}</label>
  </div>
)

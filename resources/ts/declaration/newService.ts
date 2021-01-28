export interface QuotaValue {
  number: string,
  time: string,
  per: string
}

export interface ErrorMessage {
  time: string[],
  quota: string,
  day: string,
  doctor: string,
  service: string
}

export interface ButtonData {
  className: string,
  color: string,
  text: string,
  disabled?: boolean,
  type?: 'button' | 'submit',
  onclick?: () => any
}
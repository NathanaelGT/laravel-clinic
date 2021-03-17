import validator from './validator'

export default (element: HTMLElement) => {
  if (element.contentEditable === 'true') return

  const validate = validator(element)

  element.contentEditable = 'true'
  element.onkeypress = event => {
    if (event.which === 13) {
      event.preventDefault()

      if (element.dataset.id === 'new') {
        return validate(false)
      }

      element.blur()
    }
  }

  element.oninput = () => {
    if (element.innerText === '') {
      element.blur()
    }
  }

  element.onblur = validate
}

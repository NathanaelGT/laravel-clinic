const getFragment = (children, element, fragment = document.createDocumentFragment()) => {
  children.forEach(child => {
    if (child instanceof HTMLElement) fragment.appendChild(child)
    else if (typeof child === 'string' || typeof child === 'number')
      fragment.appendChild(document.createTextNode(child))
    else if (Array.isArray(child)) getFragment(child, element, fragment)
    else if (process.env.MIX_DEBUG) {
      switch (typeof child) {
        case 'boolean':
        case 'undefined':
          break
        default:
          console.error('not appendable', element, child)
          console.trace()
      }
    }
  })
  return fragment
}

window.dom = (tag, attributes, ...children) => {
  if (typeof tag === 'function') return tag(attributes || {})

  if (attributes?.className)
    attributes.className = attributes.className.replaceAll('undefined', '').replaceAll('false', '').trim()

  const element = document.createElement(tag)
  if (attributes?.dangerouslySetInnerHTML) element.innerHTML = attributes.dangerouslySetInnerHTML
  element.appendChild(getFragment(children, element))
  Object.assign(element, attributes)
  return element
}

window.registerRender = parent => {
  parent.render = newChild => {
    const oldChild = parent.DOMchildNodes

    if (oldChild) parent.replaceChild(newChild, oldChild)
    else parent.appendChild(newChild)

    parent.DOMchildNodes = newChild
  }
  return parent
}
declare global {
  interface Window {
    dom: (
      tag: string | ((attributes: Object) => any),
      attributes: Object,
      ...children: any[]
    ) => HTMLElement,
    registerRender: (parent: HTMLElement | Element) => DOMElement,
  }

  interface DOMElement extends HTMLElement {
    DOMchildNodes: HTMLElement,
    render: (newChild: HTMLElement) => void
  }

  const React
}

export default null

const getFragment = (
  children: any[],
  element: HTMLElement,
  fragment = document.createDocumentFragment()
) => {
  children.forEach(child => {
    if (child instanceof HTMLElement) fragment.appendChild(child)
    else if (['string', 'number'].includes(typeof child)) {
      fragment.appendChild(document.createTextNode(String(child)))
    }
    else if (Array.isArray(child)) {
      getFragment(child, element, fragment)
    }
    else if (process.env.MIX_DEBUG) {
      switch (typeof child) {
        case 'boolean':
        case 'undefined':
          break
        default:
          console.error('not appendable', element, child)
          console.trace()
          break
      }
    }
  })
  return fragment
}

window.dom = (tag, attributes, ...children) => {
  if (typeof tag === 'function') return tag(attributes || {})

  if (attributes?.['className']) {
    const className = String(attributes['className']).replaceAll('undefined', '').replaceAll('false', '').trim()
    if (className) attributes['className'] = className
  }

  const element = document.createElement(tag) as HTMLElement
  if (attributes?.['dangerouslySetInnerHTML']) {
    element.innerHTML = attributes['dangerouslySetInnerHTML']
  }

  const attr = {}
  if (attributes) {
    Object.entries(attributes).forEach(([key, value]) => {
      if (key.startsWith('data-')) {
        element.setAttribute(key, value)
      }
      else attr[key] = value
    })
  }
  Object.assign(element, attr)

  element.appendChild(getFragment(children, element))

  return element
}

window.registerRender = parent => {
  parent['render'] = (newChild: HTMLElement) => {
    const oldChild = parent['DOMchildNodes']

    if (oldChild) parent.replaceChild(newChild, oldChild)
    else parent.appendChild(newChild)

    parent['DOMchildNodes'] = newChild
  }
  return parent as DOMElement
}

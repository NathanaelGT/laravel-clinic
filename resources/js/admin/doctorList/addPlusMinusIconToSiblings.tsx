declare const React

export default (elements: NodeListOf<Element>, applyLiveEdit: (_element: Element) => void) => {
  elements.forEach(day => {
    const sibling = day.nextElementSibling
    const icon = <img className="cursor-pointer" src={window.plusUrl} width="16" height="16" />

    icon.onclick = () => {
      const siblingFirstChild = sibling.children[0]
      const siblingFirstChildText = (siblingFirstChild as HTMLElement).innerText.trim()
      if (siblingFirstChildText === 'Tutup') siblingFirstChild.remove()

      if (icon.src === window.plusUrl) {
        const element = <span className="one-line text-success" />
        element.innerHTML = '<span class="editable" data-type="time" data-id="new">08:00 - 10:00</span> (per <span class="editable" data-type="per" data-id="new">30 menit</span>)'
        element.querySelectorAll('.editable').forEach(applyLiveEdit)

        if (siblingFirstChildText !== 'Tutup') icon.previousElementSibling.innerHTML += ', '
        icon.src = window.minusUrl

        icon.insertAdjacentElement('beforebegin', element)
      }
      else {
        icon.src = window.plusUrl
        icon.previousElementSibling.remove()
        if (sibling.children[0] !== icon) {
          icon.previousElementSibling.innerHTML = icon.previousElementSibling.innerHTML.slice(0, -2)
        }
        else {
          const virtual = <virtual />
          virtual.innerHTML = `<span class="one-line">
          <span>Tutup</span>
        </span>`
          icon.parentElement.prepend(virtual.children[0])
        }
      }
    }

    sibling.appendChild(icon)
  })
}
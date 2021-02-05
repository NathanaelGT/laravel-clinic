export default (elements: NodeListOf<Element>, applyLiveEdit: (_element: Element) => void) => {
  elements.forEach(day => {
    const sibling = day.nextElementSibling
    const icon = <img className="cursor-pointer" src={window.plusUrl} width="16" height="16" /> as HTMLImageElement

    icon.onclick = () => {
      const siblingFirstChild = sibling.children[0]
      const siblingFirstChildText = (siblingFirstChild as HTMLElement).innerText.trim()

      if (icon.src === window.plusUrl) {
        const element = <span className="one-line text-success" />
        element.innerHTML = '<span class="editable" data-type="time" data-id="new">08:00 - 10:00</span> (per <span class="editable" data-type="per" data-id="new">30 menit</span>)'
        element.querySelectorAll('.editable').forEach(applyLiveEdit)

        console.log(icon, sibling)
        const index = icon.previousElementSibling.childNodes.length - 1
        if (siblingFirstChildText !== 'Tutup') icon.previousElementSibling.childNodes[index].textContent = '), '
        icon.src = window.minusUrl

        icon.insertAdjacentElement('beforebegin', element)
      }
      else {
        icon.src = window.plusUrl
        icon.previousElementSibling.remove()
        if (sibling.children[0] !== icon) {
          const index = icon.previousElementSibling.childNodes.length - 1
          icon.previousElementSibling.childNodes[index].textContent = ') '
        }
        else {
          icon.parentElement.prepend(
            <span class="one-line">
              <span>Tutup</span>
            </span>
          )
        }
      }
      if (siblingFirstChildText === 'Tutup') siblingFirstChild.remove()
    }

    sibling.appendChild(icon)
  })
}
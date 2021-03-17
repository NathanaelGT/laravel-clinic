import applyLiveEdit from './applyLiveEdit'

const plusUrl = window.origin + '/svg/plus.svg'
const minusUrl = window.origin + '/svg/minus.svg'

export default (elements: NodeListOf<Element>) => {
  elements.forEach(day => {
    const sibling = day.nextElementSibling
    const icon = <img className="cursor-pointer" src={plusUrl} width="16" height="16" /> as HTMLImageElement

    icon.onclick = () => {
      const siblingFirstChild = sibling.children[0]
      const siblingFirstChildText = (siblingFirstChild as HTMLElement).innerText.trim()

      if (icon.src === plusUrl) {
        const element = (
          <span className="one-line text-success">
            <span className="editable" data-type="time" data-id="new">08:00 - 10:00</span>{' '}
            (per <span className="editable" data-type="per" data-id="new">30 menit</span>)
          </span>
        )
        element.querySelectorAll('.editable').forEach(applyLiveEdit)

        icon.src = minusUrl
        icon.insertAdjacentElement('beforebegin', element)
      }
      else {
        icon.src = plusUrl
        icon.previousElementSibling.remove()
        if (sibling.children[0] === icon) {
          icon.parentElement.prepend(
            <span className="one-line">
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

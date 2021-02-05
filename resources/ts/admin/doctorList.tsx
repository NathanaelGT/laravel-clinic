declare global {
  interface Window {
    plusUrl: string,
    minusUrl: string
  }
}

import addPlusMinusIconToSiblings from './doctorList/addPlusMinusIconToSiblings'
import deleteTableRecord from './doctorList/deleteTableRecord'
import { fetching, validate } from './doctorList/logic'

const applyLiveEdit = (element: HTMLElement) => {
  let text = element.innerText

  element.contentEditable = 'true'
  element.onkeypress = event => {
    if (event.which === 13) {
      event.preventDefault()
      element.blur()
    }
  }

  element.oninput = () => {
    if (element.innerText === '') setTimeout(() => element.blur(), 0)
  }

  element.onblur = event => {
    let newText = (event.target as HTMLElement).innerText.trim()

    if (element.dataset.type === 'name') {
      if (newText === '') return element.innerText = text
    }
    else if (newText === '' || newText.toLowerCase() === 'tutup') {
      const parent = element.parentElement
      const grandParent = parent.parentElement
      const day = (grandParent.previousElementSibling as HTMLElement).innerText.slice(0, -1)

      if (grandParent.childElementCount === 2) {
        if (confirm(`Apakah anda yakin ingin menutup jadwal pada hari ${day}?`)) {
          fetching.close(element)

          grandParent.children[0].remove()
          grandParent.prepend(
            <span className="one-line">
              <span>Tutup</span>
            </span>
          )

          const rowElement = grandParent.parentElement.parentElement.parentElement
          const blank = Array.from(rowElement.children).every(child => (
            Array.from(child.children).every(grandChild => (
              (grandChild.children[1] as HTMLElement).innerText.trim() === 'Tutup'
            ))
          ))
          if (blank) deleteTableRecord(rowElement.parentElement)
          return
        }
      }
      else {
        const grandParentChild = Array.from(grandParent.children)
        const parentNthChild = grandParentChild.indexOf(parent) + 1
        if (confirm(`Apakah anda yakin ingin menghapus sesi ke ${parentNthChild} pada hari ${day}?`)) {
          const parentSibling = parent.previousElementSibling
          if (parentSibling) {
            parent.previousElementSibling.childNodes[4].replaceWith(document.createTextNode(') '))
          }
          fetching.close(element)
          return parent.remove()
        }
      }
      return element.innerText = text
    }

    const validation = validate[element.dataset.type](newText, element)
    if (validation) {
      element.title = validation
      return element.classList.add('text-danger')
    }
    else {
      newText = element.innerText.trim() //ada kemungkinan validasinya ngubah textnya
      element.title = ''
    }
    element.classList.remove('text-danger')

    if (newText === text) return
    if (element.dataset.id === 'new') {
      if (!confirm(`Apakah anda yakin ingin menambahkan jadwal ini?\n\n${newText}`)) {
        return element.innerText = text
      }
    }
    else {
      if (!confirm(`Apakah anda yakin ingin menyimpan perubahan ini?\n\n${text}\nMenjadi\n${newText}`))
        return element.innerText = text
    }
    if (element.dataset.id === 'new') {
      const parent = element.parentElement
      parent.classList.remove('text-success');
      (parent.nextElementSibling as HTMLImageElement).src = window.plusUrl
    }

    text = newText
    const type = element.dataset.type
    fetching[type === 'time' || type === 'per' ? 'service' : type](element, newText)
  }
}

document.querySelectorAll<HTMLElement>('.editable').forEach(applyLiveEdit)
addPlusMinusIconToSiblings(document.querySelectorAll('.day'), applyLiveEdit)
document.querySelectorAll<HTMLFormElement>('form.delete-service').forEach(form => {
  form.onsubmit = event => {
    event.preventDefault()
    deleteTableRecord(form.parentElement.previousElementSibling as HTMLElement)
  }
})
declare global {
  interface Window {
    plusUrl: string,
    minusUrl: string
  }
}

declare const React

//@ts-ignore
import addPlusMinusIconToSiblings from './doctorList/addPlusMinusIconToSiblings.tsx'
//@ts-ignore
import deleteTableRecord from './doctorList/deleteTableRecord.ts'

const update = (url: string, element: HTMLElement | null, data: object) => {
  if (element) {
    element.contentEditable = 'false'
    element.classList.add('text-secondary')
  }

  // console.log(window.location.origin + '/api/' + url + '/' + id, data)
  fetch(window.location.origin + /api/ + url + '/' + element.dataset.id, {
    method: 'POST',
    body: JSON.stringify(data)
  }).then(res => {
    if (res.ok && element) {
      element.contentEditable = 'true'
      element.classList.remove('text-secondary')
    }
    return res.json()
  }).then(res => {
    if (element.dataset.id === 'new') {
      element.dataset.id = res.id

      const previousSibling = element.previousElementSibling
      if (previousSibling) {
        previousSibling.setAttribute('data-id', res.id)
      }
      else {
        element.nextElementSibling.setAttribute('data-id', res.id)
      }
    }
  })
}

const validate = {
  name() {
    return true
  },

  time(value: string) {
    const times = value.split(' - ')
    if (times.length !== 2) return false

    return times.every(time => {
      if (time.length !== 5) return false

      const [hour, minute] = time.split(':').map(val => Number(val))

      if (isNaN(hour) || hour >= 24 || hour < 0) return false
      if (isNaN(minute) || minute >= 60 || minute < 0) return false
      return true
    })
  },

  per(value: string) {
    const [quota, per] = value.split(' ')
    if (!per) return false

    if (isNaN(Number(quota)) && Number(quota) < 1) return false

    const validPer = ['sesi', 'jam', 'menit']
    if (validPer.indexOf(per.toLowerCase()) === -1) return false

    return true
  }
}

const fetching = {
  name(element: HTMLElement, name: string) {
    update('doctor', element, { name })
  },
  time(element: HTMLElement, time: string, doctorServiceId: string) {
    update('serviceTime', element, { time, doctorServiceId })
  },
  per(element: HTMLElement, per: string, doctorServiceId: string) {
    update('servicePer', element, { per, doctorServiceId })
  },
  close(element: HTMLElement, ids: string[]) {
    update('close', element, { ids })
  },
  deleteService(id: string) {
    update('delete', null, { id })
  }
}

const applyLiveEdit = (_element: Element) => {
  const element = _element as HTMLElement
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
    const newText = (event.target as HTMLElement).innerText.trim()

    if (element.dataset.type !== 'name' && (newText === '' || newText.toLowerCase() === 'tutup')) {
      const parent = element.parentElement
      const grandParent = parent.parentElement
      const day = (grandParent.previousElementSibling as HTMLElement).innerText.slice(0, -1)

      if (grandParent.childElementCount === 2) {
        if (confirm(`Apakah anda yakin ingin menutup jadwal pada hari ${day}?`)) {
          const ids = []
          grandParent.querySelectorAll('*[data-id]').forEach(element => {
            const id = element.getAttribute('data-id')
            if (ids.indexOf(id) === -1) ids.push(id)
          })
          fetching.close(element, ids)

          grandParent.children[0].remove()
          grandParent.prepend(
            <span className="one-line">
              <span>Tutup</span>
            </span>
          )

          const rowElement = grandParent.parentElement.parentElement.parentElement
          const blank = Array.from(rowElement.children).every(child => (
            Array.from(child.children).every(grandChild => (
              (grandChild.children[1] as HTMLElement).innerText === 'Tutup'
            ))
          ))
          if (blank) deleteTableRecord(rowElement, fetching.deleteService)
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
          fetching.close(element, [element.dataset.id])
          return parent.remove()
        }
      }
      return element.innerText = text
    }

    if (!validate[element.dataset.type](newText)) return element.classList.add('text-danger')
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
    const doctorServiceId = element.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling?.getAttribute('data-id')
    fetching[element.dataset.type](element, newText, doctorServiceId)
  }
}

document.querySelectorAll('.editable').forEach(applyLiveEdit)
addPlusMinusIconToSiblings(document.querySelectorAll('.day'), applyLiveEdit)
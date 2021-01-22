import addPlusMinusIconToSiblings from './doctorList/addPlusMinusIconToSiblings'
import deleteTableRecord from './doctorList/deleteTableRecord'
import closeElement from './doctorList/closeElement'

const update = (url, element, data) => {
  element.contentEditable = false
  element.classList.add('text-secondary')

  // console.log(window.location.origin + '/api/' + url + '/' + id, data)
  fetch(window.location.origin + /api/ + url + '/' + element.dataset.id, {
    method: 'POST',
    body: JSON.stringify(data)
  }).then(res => {
    if (res.ok) {
      element.contentEditable = true
      element.classList.remove('text-secondary')
    }
    return res.json()
  }).then(res => {
    console.log(res)
  })
}

const validate = {
  name() {
    return true
  },

  time(value) {
    const times = value.split(' - ')
    if (times.length !== 2) return false

    return times.every(time => {
      if (time.length !== 5) return false

      let [hour, minute] = time.split(':')
      hour = Number(hour)
      minute = Number(minute)

      if (isNaN(hour) || hour >= 24 || hour < 0) return false
      if (isNaN(minute) || minute >= 60 || minute < 0) return false
      return true
    })
  },

  per(value) {
    const [quota, per] = value.split(' ')
    if (!per) return false

    if (isNaN(Number(quota)) && Number(quota) < 1) return false

    const validPer = ['sesi', 'jam', 'menit']
    if (validPer.indexOf(per.toLowerCase()) === -1) return false

    return true
  },

  close(value) {

  }
}

const fetching = {
  name(element, name) {
    update('doctor', element, { name })
  },
  time(element, time, doctorServiceId) {
    update('serviceTime', element, { time, doctorServiceId })
  },
  per(element, per, doctorServiceId) {
    update('servicePer', element, { per, doctorServiceId })
  },
  close(element, ids) {
    update('close', element, { ids })
  },
  deleteService(id) {
    update('delete', <virtual />, { id })
  }
}

const applyLiveEdit = element => {
  let text = element.innerText

  element.contentEditable = true
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
    const newText = event.target.innerText.trim()
    console.log(element.dataset.type)

    if (element.dataset.type !== 'name' && (newText === '' || newText.toLowerCase() === 'tutup')) {
      const parent = element.parentElement
      const grandParent = parent.parentElement
      const day = grandParent.previousElementSibling.innerText.slice(0, -1)

      if (grandParent.childElementCount === 2) {
        if (confirm(`Apakah anda yakin ingin menutup jadwal pada hari ${day}?`)) {
          const ids = []
          grandParent.querySelectorAll('*[data-id]').forEach(element => {
            const id = element.dataset.id
            if (ids.indexOf(id) === -1) ids.push(id)
          })
          fetching.close(element, ids)

          grandParent.innerHTML = closeElement

          const rowElement = grandParent.parentElement.parentElement.parentElement
          const blank = Array.from(rowElement.children).every(child => (
            Array.from(child.children).every(grandChild => (
              grandChild.children[1].innerText === 'Tutup'
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
      parent.classList.remove('text-success')
      parent.nextElementSibling.src = window.plusUrl
    }

    text = newText
    const doctorServiceId = element.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling?.dataset.id
    fetching[element.dataset.type](element, newText, doctorServiceId)
  }
}

document.querySelectorAll('.editable').forEach(applyLiveEdit)
addPlusMinusIconToSiblings(document.querySelectorAll('.day'), applyLiveEdit)
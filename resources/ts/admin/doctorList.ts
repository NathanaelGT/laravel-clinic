import addPlusMinusIconToSiblings from './doctorList/addPlusMinusIconToSiblings'
import deleteTableRecord from './doctorList/deleteTableRecord'
import applyLiveEdit from './doctorList/applyLiveEdit'
import dragable from './doctorList/dragable'


addPlusMinusIconToSiblings(document.querySelectorAll('.day'))
document.querySelectorAll<HTMLElement>('.editable').forEach(applyLiveEdit)
document.querySelectorAll<HTMLFormElement>('form.delete-service').forEach(form => {
  form.onsubmit = event => {
    event.preventDefault()
    deleteTableRecord(form.parentElement.previousElementSibling as HTMLElement)
  }
})

dragable(document.querySelector<HTMLElement>('tbody'))

const today = new Date().toLocaleDateString('id', { weekday: 'long' })
const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']

const showDetail = []
const handleDivClick = ({ index, schedule, doctor }: Props) => () => {
  showDetail[index] = !showDetail[index]
  containers[index].render(<HourInfo index={index} schedule={schedule} doctor={doctor} />)
}

interface Props {
  index: number
  schedule: any
  doctor: string
}

const HourInfo = ({ index, schedule, doctor }: Props) => (
  <div onclick={handleDivClick({ index, schedule, doctor })} className={showDetail[index] && 'dropup'}>
    <p className="card-text mb-1 service-info dropdown-toggle">
      Jam praktek {doctor}{showDetail[index] ? ':' : ' hari ini:'}
    </p>
    {showDetail[index] ? (
      <div>
        {days.map(day => (
          <p className="card-text mb-0 d-flex">
            <span>{day === today ? <strong>{day}</strong> : day}: </span>
            <span>{schedule[day] || 'Tutup'}</span>
          </p>
        ))}
      </div>
    ) : (
      <p className="card-text mb-0">{schedule[today] || 'Tutup'}</p>
    )}
  </div>
)

const containers = document.querySelectorAll<DOMElement>('.service-card')
containers.forEach((container, index) => {
  const element = container.querySelector<HTMLElement>('span.bold')
  const schedule = JSON.parse(element.getAttribute('data-schedule'))
  const doctor = element.innerText

  window.registerRender(container).render(<HourInfo index={index} schedule={schedule} doctor={doctor} />)
})

const doctorsName = document.querySelectorAll<HTMLElement>('.doctors-name')
doctorsName.forEach((doctorsName, index) => {
  const doctorsNameChildren = Array.from(doctorsName.children) as HTMLElement[]
  doctorsNameChildren.forEach(doctorName => {
    const schedule = JSON.parse(doctorName.getAttribute('data-schedule'))
    const doctor = doctorName.innerText

    doctorName.removeAttribute('data-schedule')
    doctorName.onclick = () => {
      doctorsNameChildren.forEach(doctorName => {
        doctorName.classList.remove('bold')
        doctorName.classList.add('text-muted')
      })
      doctorName.classList.remove('text-muted')
      doctorName.classList.add('bold')

      containers[index].render(<HourInfo index={index} schedule={schedule} doctor={doctor} />)
    }
  })
})

const today = new Date().toLocaleDateString('id', { weekday: 'long' })
const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']

const showDetail = []
handleDivClick = (index, scheduleIndex, doctor) => () => {
  showDetail[index] = !showDetail[index]
  containers[index].render(<HourInfo index={index} schedule={scheduleIndex} doctor={doctor} />)
}

const HourInfo = ({ index, schedule, doctor }) => (
  <div onclick={handleDivClick(index, schedule, doctor)} className={showDetail[index] && 'dropup'}>
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

const containers = document.querySelectorAll('.service-card')
containers.forEach((container, index) => {
  const element = container.querySelector('span.bold')
  const schedule = JSON.parse(element.getAttribute('data-schedule'))
  const doctor = element.innerText

  registerRender(container).render(<HourInfo index={index} schedule={schedule} doctor={doctor} />)
})

const doctorsName = document.querySelectorAll('.doctors-name')
doctorsName.forEach((doctorsName, index) => {
  const doctorsNameChildren = Array.from(doctorsName.children)
  doctorsNameChildren.forEach(doctorName => {
    const schedule = JSON.parse(doctorName.getAttribute('data-schedule'))
    const doctor = doctorName.innerText

    doctorName.removeAttribute('data-schedule')
    doctorName.onclick = () => {
      doctorsNameChildren.forEach(doctorName => {
        doctorName.classList.remove('bold')
      })
      doctorName.classList.add('bold')

      containers[index].render(<HourInfo index={index} schedule={schedule} doctor={doctor} />)
    }
  })
})
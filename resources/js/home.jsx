const today = new Date().toLocaleDateString('id', { weekday: 'long' })
const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']

const showDetail = []
handleDivClick = (index, scheduleIndex) => () => {
  showDetail[index] = !showDetail[index]
  containers[index].render(<HourInfo index={index} scheduleIndex={scheduleIndex} />)
}

const HourInfo = ({ index, scheduleIndex }) => (
  <div onclick={handleDivClick(index, scheduleIndex)}>
    <p className="card-text me-1 mb-1 service-info">
      Jam praktek Dr. {doctors[index][scheduleIndex]}{showDetail[index] ? ':' : ' hari ini:'}
    </p>
    {showDetail[index] ? (
      <div>
        {days.map(day => {
          const hours = workingSchedules[index][scheduleIndex][day]

          return (
            <p className="card-text mb-0 d-flex">
              <span>{day === today ? <strong>{day}</strong> : day}: </span>
              <span>
                {hours?.map((hour, index) => (
                  <span>{hour}{index !== hours.length - 1 && ','}&nbsp;</span>
                )) || <span>Tutup</span>}
              </span>
            </p>
          )
        })}
      </div>
    ) : (
        <p className="card-text mb-0">
          {workingSchedules[index][scheduleIndex][today]?.join(', ') || 'Tutup'}
        </p>
      )}
  </div>
)

const containers = Array.from(document.getElementsByClassName('service-card'))
containers.forEach((container, index) => {
  registerRender(container).render(<HourInfo index={index} scheduleIndex={0} />)
})

const doctorsName = Array.from(document.getElementsByClassName('doctors-name'))
doctorsName.forEach((doctorsName, index) => {
  Array.from(doctorsName.children).forEach((doctorName, secondIndex) => {
    doctorName.onclick = () => {
      containers[index].render(<HourInfo index={index} scheduleIndex={secondIndex} />)
    }
  })
})
const containers = Array.from(document.getElementsByClassName('service-card'))
containers.forEach(registerRender)

const today = new Date().toLocaleDateString('id', { weekday: 'long' })
const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']

const showDetail = Array(containers.length).fill(false)
handleDivClick = index => () => {
  showDetail[index] = !showDetail[index]
  containers[index].render(HourInfo(index))
}

const HourInfo = index => (
  <div onclick={handleDivClick(index)}>
    <p className="card-text me-1 mb-1 service-info">Jam praktek{showDetail[index] ? ':' : ' hari ini:'}</p>
    {showDetail[index] ? (
      <div>
        {days.map(day => {
          const hours = workingHours[index][day]

          return (
            <p className="card-text mb-0 d-flex">
              <span>{day}: </span>
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
        <p className="card-text mb-0">{workingHours[index][today]?.join(', ') || 'Tutup'}</p>
      )}
  </div>
)

containers.forEach((container, index) => {
  container.render(HourInfo(index))
})
import Input from './Input'
import MultipleInput from './MultipleInput'
import InlineCheckbox from './InlineCheckbox'

export default data => {
  const { handleSubmit, errorMessages, currentIndex, getButtonsData } = data
  const updateQuotaValue = field => event => {
    data.quotaValue[currentIndex][field] = event.target.value
  }
  console.log(currentIndex, data.quotaValue)

  return (
    <form className="d-flex flex-column justify-content-between align-items-center h-100" onsubmit={handleSubmit}>
      <div className="w-100">
        <h3 className="text-center pb-4">Buat Layanan Baru</h3>

        <div className="py-1">
          <Input
            placeholder="Nama Dokter"
            name="service"
            value={data.doctorName}
            oninput={event => {
              data.doctorName = event.target.value
            }}
            currentIndex={currentIndex}
            errorMessage={errorMessages[currentIndex]?.doctor}
          />
        </div>

        <div className="py-1">
          <Input
            placeholder="Nama Layanan"
            name="service"
            value={data.serviceName}
            oninput={event => {
              data.serviceName = event.target.value
            }}
            errorMessage={errorMessages[currentIndex]?.service}
            list="services"
          />

          <datalist id="services">
            {window.services.map(service => (
              <option value={service} />
            ))}
          </datalist>
        </div>

        <div className="row py-1">
          {[
            { placeholder: 'Jam Mulai Praktek', name: 'time time-start' },
            { placeholder: 'Jam Selesai Praktek', name: 'time time-end' }
          ].map((_data, index) => (
            <div className={`col-md-6 col-sm-12 p${(window.innerWidth >= 768) && (index === 0 ? 's' : 'e')}-0`}>
              <Input
                {..._data}
                type="time"
                oninput={event => {
                  data.timeInputValue[currentIndex][index] = event.target.value
                }}
                value={data.timeInputValue?.[currentIndex]?.[index]}
                errorMessage={errorMessages[currentIndex]?.time?.[index]}
              />
            </div>
          ))}
        </div>

        <MultipleInput
          name="test"
          leftPlaceholder="Kuota"
          leftOninput={updateQuotaValue('number')}
          leftValue={data.quotaValue[currentIndex].number}
          separator="Per"
          middlePlaceholder="Waktu"
          middleOninput={updateQuotaValue('time')}
          middleValue={data.quotaValue[currentIndex].time}
          rightPlaceholder="Satuan"
          rightOninput={updateQuotaValue('per')}
          rightValue={data.quotaValue[currentIndex].per}
          options={['Sesi', 'Jam', 'Menit']}
          errorMessage={errorMessages[currentIndex]?.quota}
        />
      </div>

      <div className="w-100">
        <div className="d-flex justify-content-center pb-1">
          Hari Praktek
      </div>

        <div className="d-flex flex-column flex-sm-row justify-content-center">
          {[['Senin', 'Selasa', 'Rabu'], ['Kamis', 'Jumat', 'Sabtu']].map(days => (
            <div className="d-flex justify-content-center py-1">
              {days.map(value => (
                <InlineCheckbox
                  value={value}
                  checked={data.dayValue[currentIndex].indexOf(value) !== -1}
                  onchange={event => {
                    if (event.target.checked)
                      data.dayValue[currentIndex].push(value)
                    else
                      data.dayValue[currentIndex] = data.dayValue[currentIndex].filter(day => day !== value)
                  }}
                />
              ))}
            </div>
          ))}
        </div>
        <div className="d-flex justify-content-center">
          <div className={`form-text text-danger ${!errorMessages[currentIndex]?.day && 'input-valid-text'}`}>
            {errorMessages[currentIndex]?.day || '\xA0'}
          </div>
        </div>

        <div className="row my-4">
          {getButtonsData().map(({ className, color, text, type, disabled = false, onclick = null }) => (
            <div className={className}>
              <div className="d-grid">
                <button
                  type={type || 'button'}
                  className={`btn btn-${color}`}
                  disabled={disabled}
                  onclick={onclick}
                >
                  {text}
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </form>
  )
}
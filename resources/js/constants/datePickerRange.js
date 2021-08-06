
const rangeDataToday = new Date()
let rangeDataYesterday = new Date()
rangeDataYesterday.setDate(rangeDataToday.getDate() - 1)

let dateLastWeek = new Date()
let rangeDataLastWeekEnd = new Date()
let rangeDataLastWeekStart = new Date()
rangeDataLastWeekEnd.setTime(dateLastWeek.setTime(dateLastWeek.getTime()-(dateLastWeek.getDay()?dateLastWeek.getDay():7)*24*60*60*1000))
rangeDataLastWeekStart.setTime(dateLastWeek.setTime(rangeDataLastWeekEnd.getTime()-6*24*60*60*1000))

let dateLast2Week = new Date()
let rangeDataLast2WeekEnd = new Date()
let rangeDataLast2WeekStart = new Date()
rangeDataLast2WeekEnd.setTime(dateLast2Week.setTime(dateLast2Week.getTime()-(dateLast2Week.getDay()?dateLast2Week.getDay():7)*24*60*60*1000))
rangeDataLast2WeekStart.setTime(dateLast2Week.setTime(rangeDataLastWeekEnd.getTime()-13*24*60*60*1000))

const ranges = {
  'Today': [rangeDataToday, rangeDataToday],
  'Yesterday': [rangeDataYesterday, rangeDataYesterday],
  'This month': [new Date(rangeDataToday.getFullYear(), rangeDataToday.getMonth(), 1), new Date(rangeDataToday.getFullYear(), rangeDataToday.getMonth() + 1, 0)],
  'This year': [new Date(rangeDataToday.getFullYear(), 0, 1), new Date(rangeDataToday.getFullYear(), 11, 31)],
  'Last month': [new Date(rangeDataToday.getFullYear(), rangeDataToday.getMonth() - 1, 1), new Date(rangeDataToday.getFullYear(), rangeDataToday.getMonth(), 0)],
  'Last week': [rangeDataLastWeekStart, rangeDataLastWeekEnd],
  'Last 2 weeks': [rangeDataLast2WeekStart, rangeDataLast2WeekEnd],
}

export default ranges;
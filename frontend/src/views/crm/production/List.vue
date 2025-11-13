<template>
    <JqxScheduler ref="myScheduler"
        :width="getWidth" :height="600" :source="dataAdapter" :date="date" :view="'weekView'" :resources="resources" 
        :appointmentDataFields="appointmentDataFields" :views="views" :showLegend="true" :dayNameFormat="'abbr'"
    />
</template>
<script>
    import JqxScheduler from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxscheduler.vue'
    export default {
        components: {
            JqxScheduler
        },
        data: function () {
            return {
				getWidth: "100%",
                date: new jqx.date(2021, 11, 23),
                appointmentDataFields: 
                {
                    from: 'start',
                    to: 'end',
                    id: 'id',
                    description: 'description',
                    location: 'place',
                    subject: 'subject',
                    resourceId: 'calendar'
                },
                resources:
                {
                    colorScheme: 'scheme05',
                    dataField: 'calendar',
                    orientation: 'horizontal',
                    source: new jqx.dataAdapter(this.source)
                },
                views:
                [        
                    { type: 'dayView', showWeekends: false },
                    { type: 'weekView', showWeekends: false },
                    { type: 'monthView' }
                ]
            }
        },
        beforeCreate: function () { 
            const generateAppointments = function () {
                const appointments = new Array();
                const appointment1 = {
                    id: 'id1',
                    description: 'George brings projector for presentations.',
                    location: '',
                    subject: 'W-12345',
                    calendar: 'Workshop 1',
                    start: new Date(2021, 11, 23, 9, 0, 0),
                    end: new Date(2021, 11, 30, 16, 0, 0)
                };
                const appointment2 = {
                    id: 'id2',
                    description: '',
                    location: '',
                    subject: 'W-12346',
                    calendar: 'Workshop 2',
                    start: new Date(2021, 11, 24, 10, 0, 0),
                    end: new Date(2021, 11, 24, 15, 0, 0)
                };
                const appointment3 = {
                    id: 'id3',
                    description: '',
                    location: '',
                    subject: 'W-123458',
                    calendar: 'Workshop 1',
                    start: new Date(2021, 11, 27, 11, 0, 0),
                    end: new Date(2021, 11, 27, 13, 0, 0)
                };
                const appointment4 = {
                    id: 'id4',
                    description: '',
                    location: '',
                    subject: 'W-123459',
                    calendar: 'Workshop 2',
                    start: new Date(2021, 11, 23, 0, 0, 0),
                    end: new Date(2016, 11, 25, 23, 59, 59)
                };
                const appointment5 = {
                    id: 'id5',
                    description: '',
                    location: '',
                    subject: 'W-123460',
                    calendar: 'Workshop 1',
                    start: new Date(2021, 11, 25, 15, 0, 0),
                    end: new Date(2016, 11, 25, 17, 0, 0)
                };
                const appointment6 = {
                    id: 'id6',
                    description: '',
                    location: '',
                    subject: 'W-123461',
                    calendar: 'Workshop 2',
                    start: new Date(2021, 11, 26, 14, 0, 0),
                    end: new Date(2021, 11, 26, 16, 0, 0)
                };
                appointments.push(appointment1);
                appointments.push(appointment2);
                appointments.push(appointment3);
                appointments.push(appointment4);
                appointments.push(appointment5);
                appointments.push(appointment6);
                return appointments;
            };
            this.source =
                {
                    dataType: 'array',
                    dataFields: [
                        { name: 'id', type: 'string' },
                        { name: 'description', type: 'string' },
                        { name: 'location', type: 'string' },
                        { name: 'subject', type: 'string' },
                        { name: 'calendar', type: 'string' },
                        { name: 'start', type: 'date' },
                        { name: 'end', type: 'date' }
                    ],
                    id: 'id',
                    localData: generateAppointments()
                };
            this.dataAdapter = new jqx.dataAdapter(this.source);
        },
        mounted: function () {
            this.$refs.myScheduler.ensureAppointmentVisible('id1');
        }
    }
</script>
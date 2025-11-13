<template>
  <vs-row vs-justify="center">
    <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="12" vs-xs="12">
      <vs-card>
        <full-calendar class="w-100" :config="config" :events="events">
          <div slot="fc-header-right" class="d-flex justify-content-end">
            <vs-button icon="add" color="success" @click="addEvent(new Date())">Add Event</vs-button>
          </div>
        </full-calendar>
        <!---Add Event --->
        <vs-prompt
          color="primary"
          @cancel="title=''"
          @accept="acceptEvent"
          @close="close"
          :is-valid="validName"
          :active.sync="activePrompt2"
          title="Add Event"
        >
          <div class="con-exemple-prompt">
            <vs-input label="Title" v-model="title" class="w-100 mb-4" />
            <vs-input label="Start Date" type="date" class="w-100 mb-4" v-model="start" />
            <vs-input label="End Date" type="date" class="w-100 mb-4" v-model="end" />
            <vs-alert
              :active="!validName"
              color="danger"
              icon="new_releases"
              class="mb-3"
            >Fields can not be empty please enter the data</vs-alert>
          </div>
        </vs-prompt>
      </vs-card>
    </vs-col>
  </vs-row>
</template>

<script>
import FullCalendar from "vue-fullcalendar";

export default {
  name: "Calendar",
  data: () => ({
    title: "Calendar",
    activePrompt2: false,
    start: "",
    end: "",
    desc: "",
    config: {
      defaultView: "month"
    },
    props: {
      date: {
        type: Number
      }
    }
  }),
  methods: {
    clearData() {
      this.title = this.end = this.desc = "";
    },
    addEvent() {
      this.clearData();
      this.activePrompt2 = true;
    },
    acceptEvent() {
      this.$vs.notify({
        color: "success",
        title: "Event Added",
        text: "Event Added Successfully"
      });
      const eventobject = {
        title: this.title,
        start: this.start,
        end: this.end
      };
      this.$store.dispatch("addCalendarEvent", eventobject);
    },
    close() {
      this.$vs.notify({
        color: "danger",
        title: "Closed",
        text: "You close a dialog!"
      });
    }
  },
  computed: {
    events() {
      return this.$store.state.calendarEvents;
    },
    validName() {
      return this.title.length > 0;
    }
  },
  components: {
    FullCalendar
  }
};
</script>
<style >
.comp-full-calendar {
  max-width: 100%;
}
.full-calendar-header .header-center .title {
  font-size: 18px;
  font-weight: 600;
}
.full-calendar-header .header-center .prev-month,
.full-calendar-header .header-center .next-month {
  padding: 9px 14px;
  border-radius: 100%;
  border: 1px solid #dadada;
}
.full-calendar-body
  .dates
  .dates-events
  .events-week
  .events-day
  .event-box
  .event-item {
  background-color: rgba(var(--vs-primary), 1);
  color: #fff;
}
</style>
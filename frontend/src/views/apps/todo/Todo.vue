<template>
  <div>
    <vs-card>
      <div class="d-md-flex align-items-center pb-2">
        <h3 class="card-title mb-0">Add your task and change the status from below</h3>
        <div class="ml-auto">
          <vs-button @click="active=!active" color="primary" type="filled" class="mt-4 mt-md-0">
            <i class="mdi mdi-border-color mr-1"></i> Add Todo
          </vs-button>
        </div>
      </div>
      <hr class="custom-hr" />
      <vs-row vs-justify="center" class="mt-5">
        <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="3" vs-xs="12">
          <h4 class="card-title">TO DOS</h4>
          <draggable :list="todos" group="custom" class="cursor-move todo-cards">
            <vs-card class="shadow-none" v-for="(todolist, index) in todos" :key="index">
              <div class="d-flex align-items-center mb-3">
                <h5 class="card-title mb-0 w-100 text-truncate">{{ todolist.todotitle }}</h5>
                <div class="ml-auto">
                  <span
                    class="badge badge-danger rounded-0"
                    :class="[{'display-none' : todolist.badgeactive}, {'active': isActive} ]"
                  >
                    <i class="mdi mdi-bookmark"></i>
                  </span>
                </div>
              </div>

              <p class="text-muted mb-0">{{ todolist.todosubtitle }}</p>
            </vs-card>
          </draggable>
        </vs-col>
        <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="3" vs-xs="12">
          <h4 class="card-title">IN PROGRESS</h4>
          <draggable :list="progresstodo" group="custom" class="cursor-move progress-cards">
            <vs-card class="shadow-none" v-for="(todolist, index) in progresstodo" :key="index">
              <div class="d-flex align-items-center mb-3">
                <h5 class="card-title mb-0 w-100 text-truncate">{{ todolist.todotitle }}</h5>
                <div class="ml-auto">
                  <span
                    class="badge badge-danger rounded-0"
                    :class="{'d-none' : todolist.badgeactive}"
                  >
                    <i class="mdi mdi-bookmark"></i>
                  </span>
                </div>
              </div>
              <p class="text-muted mb-0">{{ todolist.todosubtitle }}</p>
            </vs-card>
          </draggable>
        </vs-col>
        <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="3" vs-xs="12">
          <h4 class="card-title">COMPLETED</h4>
          <draggable :list="completetodo" group="custom" class="cursor-move completed-cards">
            <vs-card class="shadow-none" v-for="(todolist, index) in completetodo" :key="index">
              <div class="d-flex align-items-center mb-3">
                <h5 class="card-title mb-0 w-100 text-truncate">{{ todolist.todotitle }}</h5>
                <div class="ml-auto">
                  <span
                    class="badge badge-danger rounded-0"
                    :class="{'d-none' : todolist.badgeactive}"
                  >
                    <i class="mdi mdi-bookmark"></i>
                  </span>
                </div>
              </div>

              <p class="text-muted mb-0">{{ todolist.todosubtitle }}</p>
            </vs-card>
          </draggable>
        </vs-col>
        <vs-col type="flex" vs-justify="center" vs-align="center" vs-lg="3" vs-xs="12">
          <h4 class="card-title">ON HOLD</h4>
          <draggable :list="onholdtodo" group="custom" class="cursor-move hold-cards">
            <vs-card class="shadow-none" v-for="(todolist, index) in onholdtodo" :key="index">
              <div class="d-flex align-items-center mb-3">
                <h5 class="card-title mb-0 w-100 text-truncate">{{ todolist.todotitle }}</h5>
                <div class="ml-auto">
                  <span
                    class="badge badge-danger rounded-0"
                    :class="{'d-none' : todolist.badgeactive}"
                  >
                    <i class="mdi mdi-bookmark"></i>
                  </span>
                </div>
              </div>
              <p class="text-muted mb-0">{{ todolist.todosubtitle }}</p>
            </vs-card>
          </draggable>
        </vs-col>
      </vs-row>

      <vs-sidebar
        position-right
        parent="body"
        default-index="1"
        color="primary"
        class="sidebarx"
        spacer
        v-model="active"
      >
        <div class="header-sidebar p-3 bg-light">
          <div class="d-flex align-items-center">
            <h3 class="card-title mb-0">Add Your Task</h3>
            <div class="ml-auto">
              <vs-button color="primary" large radius icon="event_note"></vs-button>
            </div>
          </div>
        </div>
        <div class="mt-4 px-3">
          <form v-on:submit="submitForm">
            <vs-input
              icon-after="true"
              icon="mode_edit"
              placeholder="Todo Title"
              v-model="todotitle"
              class="w-100 mb-4"
            />
            <vs-input
              icon-after="true"
              icon="mode_edit"
              placeholder="Todo Subtitle"
              v-model="todosubtitle"
              class="w-100 mb-4"
            />
            <div class="d-flex">
              <vs-checkbox
                v-model="todos.checked"
                id="bookmk"
                value
                @click="showBookmark()"
                :class="{checked:isChk.includes('addbook')}"
                for="addbook"
              >Add to Bookmark</vs-checkbox>
            </div>
            <vs-button color="primary" type="filled" class="w-100 mt-4" @click="addTodo()">
              <i class="mdi mdi-library-plus mr-1"></i>
              Add Task
            </vs-button>
          </form>
        </div>
      </vs-sidebar>
    </vs-card>
  </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
  name: "Todo",
  data: () => ({
    title: "Todo",
    active: false,
    todotitle: "",
    todosubtitle: "",
    bookmark: "",
    todos: [],
    isChk: [],
    isActive: false,
    badgeactive: [],
    search: "",
    todos: [
      {
        todotitle: "Book a Ticket",
        todosubtitle:
          "Blandit tempus porttitor aasfs. Integer posuere erat a ante venenatis.",
        badgeactive: true
      },
      {
        todotitle: "Launch new template ",
        todosubtitle:
          "Integer posuere erat a ante vene natis.Blandit tempus porttitor aasfs.",
        badgeactive: true
      },
      {
        todotitle: "Change a Design",
        todosubtitle:
          "Commodo luctus, nisi erat porttitor. Integer posuere erat a ante venenatis.",
        badgeactive: false
      },
      {
        todotitle: "Website Design",
        todosubtitle:
          "Dapibus ac facilisis in, egestas eget quam.Blandit tempus porttitor aasfs.",
        badgeactive: true
      }
    ],
    progresstodo: [
      {
        todotitle: "Go for lunch",
        todosubtitle:
          "Blandit tempus porttitor aasfs. Integer posuere erat a ante venenatis.",
        badgeactive: true
      },
      {
        todotitle: "Meeting at 2:00PM",
        todosubtitle:
          "Integer posuere erat a ante vene natis.Blandit tempus porttitor aasfs.",
        badgeactive: false
      },
      {
        todotitle: "Give salary to employee",
        todosubtitle:
          "Commodo luctus, nisi erat porttitor. Integer posuere erat a ante venenatis.",
        badgeactive: true
      },
      {
        todotitle: "Lunch a car",
        todosubtitle:
          "Dapibus ac facilisis in, egestas eget quam.Blandit tempus porttitor aasfs.",
        badgeactive: true
      }
    ],
    completetodo: [
      {
        todotitle: "Give Review for design",
        todosubtitle:
          "Blandit tempus porttitor aasfs. Integer posuere erat a ante venenatis.",
        badgeactive: true
      },
      {
        todotitle: "Nightout with friends",
        todosubtitle:
          "Integer posuere erat a ante vene natis.Blandit tempus porttitor aasfs.",
        badgeactive: true
      },
      {
        todotitle: "Go for Dinner",
        todosubtitle:
          "Commodo luctus, nisi erat porttitor. Integer posuere erat a ante venenatis.",
        badgeactive: false
      },
      {
        todotitle: "Design new template",
        todosubtitle:
          "Dapibus ac facilisis in, egestas eget quam.Blandit tempus porttitor aasfs.",
        badgeactive: true
      }
    ],
    onholdtodo: [
      {
        todotitle: "Meeting with Clients",
        todosubtitle:
          "Blandit tempus porttitor aasfs. Integer posuere erat a ante venenatis.",
        badgeactive: true
      },
      {
        todotitle: "Go for walk in weekends",
        todosubtitle:
          "Integer posuere erat a ante vene natis.Blandit tempus porttitor aasfs.",
        badgeactive: false
      },
      {
        todotitle: "Give review for tasty foods",
        todosubtitle:
          "Commodo luctus, nisi erat porttitor. Integer posuere erat a ante venenatis.",
        badgeactive: false
      },
      {
        todotitle: "Import some things",
        todosubtitle:
          "Dapibus ac facilisis in, egestas eget quam.Blandit tempus porttitor aasfs.",
        badgeactive: false
      }
    ]
  }),
  components: {
    draggable
  },
  methods: {
    addTodo() {
      const todo_bunch = {
        todotitle: this.todotitle,
        todosubtitle: this.todosubtitle,
        badgeactive: this.badgeactive
      };
      this.todos.unshift(todo_bunch);

      this.todotitle = "";
      this.todosubtitle = "";
      this.badgeactive = "";
    },
    showBookmark() {
      this.isActive = !this.isActive;
    },
    submitForm(e) {
      const todo_bunch = {
        todotitle: this.todotitle,
        todosubtitle: this.todosubtitle,
        badgeactive: false
      };
      this.todos.unshift(todo_bunch);

      this.todotitle = "";
      this.todosubtitle = "";
      this.badgeactive = "";
      // To prevent the form from submitting
      e.preventDefault();
    }
  }
};
</script>

<style scoped lang="scss">
.display-none {
  display: none;
}
.active:first-child {
  display: block;
}
</style>
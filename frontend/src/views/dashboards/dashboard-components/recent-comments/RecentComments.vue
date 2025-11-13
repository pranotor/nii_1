<template>
  <div>
    <!-- Header -->
    <div class="d-md-flex align-items-center">
      <div v-bar class="vs-scrollable w-100" style="height: 595px;">
        <div>
          <div
            class="comment-widgets position-relative"
            v-for="comment in comments"
            :comment="comment"
            :key="comment.commenttitle"
          >
            <!-- Comment Row -->
            <div class="d-flex flex-row comment-row mt-0 p-3 my-2">
              <div class="p-2">
                <img
                  :src="require('@/assets/images/users'+ comment.img)"
                  alt="user"
                  width="50"
                  class="rounded-circle"
                />
              </div>
              <div class="comment-text w-100 pl-3" :class="[comment.activeClass]">
                <h6 class="font-medium">{{ comment.username }}</h6>
                <span class="mb-3 d-block mt-2">{{ comment.desc }}</span>
                <div class="comment-footer">
                  <span class="text-muted float-right">{{ comment.date }}</span>
                  <span
                    class="badge badge-pill font-weight-normal"
                    :class="[comment.badgebg]"
                  >{{ comment.status }}</span>
                  <span class="action-icons">
                    <a href="javascript:void(0)" class="pl-2 align-middle">
                      <i class="ti-pencil-alt"></i>
                    </a>
                    <a href="javascript:void(0)" class="pl-2 align-middle">
                      <i class="ti-check"></i>
                    </a>
                    <a href="javascript:void(0)" class="pl-2 align-middle">
                      <i class="ti-heart"></i>
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "RecentComments",
  props: {
    comment: Object
  },
  data: () => ({
    title: "RecentComments",
    comments: [
      {
        img: "/1.jpg",
        activeClass: "",
        username: "James Anderson",
        desc:
          "Lorem Ipsum is simply dummy text of the printing and type setting industry.",
        date: "April 14, 2016",
        badgebg: "badge-primary",
        status: "Pending"
      },
      {
        img: "/2.jpg",
        activeClass: "",
        username: "Michael Jorden",
        desc:
          "Lorem Ipsum is simply dummy text of the printing and type setting industry.",
        date: "April 14, 2016",
        badgebg: "badge-success",
        status: "Approved"
      },
      {
        img: "/3.jpg",
        activeClass: "active",
        username: "Johnathan Doeting",
        desc:
          "Lorem Ipsum is simply dummy text of the printing and type setting industry.",
        date: "April 14, 2016",
        badgebg: "badge-danger",
        status: "Rejected"
      },
      {
        img: "/4.jpg",
        activeClass: "",
        username: "James Anderson",
        desc:
          "Lorem Ipsum is simply dummy text of the printing and type setting industry.",
        date: "April 14, 2016",
        badgebg: "badge-primary",
        status: "Pending"
      },
      {
        img: "/5.jpg",
        activeClass: "",
        username: "Michael Jorden",
        desc:
          "Lorem Ipsum is simply dummy text of the printing and type setting industry.",
        date: "April 14, 2016",
        badgebg: "badge-success",
        status: "Approved"
      }
    ]
  })
};
</script>
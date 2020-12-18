<template>
  <div>

    <section class="white-creamy-center">
      <h1 class="title">Selected Category</h1>

      <div class="flex-grid">

      <div class="field">
        <p class="control">
          <label class="checkbox">
            <input type="checkbox">
            A category
          </label>
        </p>
      </div>

      <div class="field">
        <p class="control">
          <label class="checkbox">
            <input type="checkbox">
            A category
          </label>
        </p>
      </div>


      <div class="field">
        <p class="control">
          <label class="checkbox">
            <input type="checkbox">
            A category
          </label>
        </p>
      </div>

      <div class="field">
        <p class="control">
          <label class="checkbox">
            <input type="checkbox">
            A category
          </label>
        </p>
      </div>

    </div>

    </section>

  </div>
</template>



<style scoped>
  .white-creamy-center{
    width: 800px;
  }

  .flex-grid > .field{
    width: 44%;
    flex: 1 auto;
    flex-wrap: wrap;
    flex-direction: column;

  }


</style>

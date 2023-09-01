// packages/forms/resources/js/components/tags-input.js
function tagsInputFormComponent({ state, splitKeys }) {
  return {
    newTag: "",
    state,
    createTag: function() {
      this.newTag = this.newTag.trim();
      if (this.newTag === "") {
        return;
      }
      if (this.state.includes(this.newTag)) {
        this.newTag = "";
        return;
      }
      this.state.push(this.newTag);
      this.newTag = "";
    },
    deleteTag: function(tagToDelete) {
      this.state = this.state.filter((tag) => tag !== tagToDelete);
    },
    input: {
      ["x-on:blur"]: "createTag()",
      ["x-model"]: "newTag",
      ["x-on:keydown"](event) {
        if (["Enter", ...splitKeys].includes(event.key)) {
          event.preventDefault();
          event.stopPropagation();
          this.createTag();
        }
      },
      ["x-on:paste"]() {
        $nextTick(() => {
          const pattern = splitKeys.map(
            (key) => key.replace(/[/\-\\^$*+?.()|[\]{}]/g, "\\$&")
          ).join("|");
          this.newTag.split(new RegExp(pattern, "g")).forEach((tag) => {
            this.newTag = tag;
            this.createTag();
          });
        });
      }
    }
  };
}
export {
  tagsInputFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGFncy1pbnB1dC5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gdGFnc0lucHV0Rm9ybUNvbXBvbmVudCh7IHN0YXRlLCBzcGxpdEtleXMgfSkge1xuICAgIHJldHVybiB7XG4gICAgICAgIG5ld1RhZzogJycsXG5cbiAgICAgICAgc3RhdGUsXG5cbiAgICAgICAgY3JlYXRlVGFnOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLm5ld1RhZyA9IHRoaXMubmV3VGFnLnRyaW0oKVxuXG4gICAgICAgICAgICBpZiAodGhpcy5uZXdUYWcgPT09ICcnKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICh0aGlzLnN0YXRlLmluY2x1ZGVzKHRoaXMubmV3VGFnKSkge1xuICAgICAgICAgICAgICAgIHRoaXMubmV3VGFnID0gJydcblxuICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLnN0YXRlLnB1c2godGhpcy5uZXdUYWcpXG5cbiAgICAgICAgICAgIHRoaXMubmV3VGFnID0gJydcbiAgICAgICAgfSxcblxuICAgICAgICBkZWxldGVUYWc6IGZ1bmN0aW9uICh0YWdUb0RlbGV0ZSkge1xuICAgICAgICAgICAgdGhpcy5zdGF0ZSA9IHRoaXMuc3RhdGUuZmlsdGVyKCh0YWcpID0+IHRhZyAhPT0gdGFnVG9EZWxldGUpXG4gICAgICAgIH0sXG5cbiAgICAgICAgaW5wdXQ6IHtcbiAgICAgICAgICAgIFsneC1vbjpibHVyJ106ICdjcmVhdGVUYWcoKScsXG4gICAgICAgICAgICBbJ3gtbW9kZWwnXTogJ25ld1RhZycsXG4gICAgICAgICAgICBbJ3gtb246a2V5ZG93biddKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgaWYgKFsnRW50ZXInLCAuLi5zcGxpdEtleXNdLmluY2x1ZGVzKGV2ZW50LmtleSkpIHtcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKVxuICAgICAgICAgICAgICAgICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKVxuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY3JlYXRlVGFnKClcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgWyd4LW9uOnBhc3RlJ10oKSB7XG4gICAgICAgICAgICAgICAgJG5leHRUaWNrKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgcGF0dGVybiA9IHNwbGl0S2V5c1xuICAgICAgICAgICAgICAgICAgICAgICAgLm1hcCgoa2V5KSA9PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGtleS5yZXBsYWNlKC9bL1xcLVxcXFxeJCorPy4oKXxbXFxde31dL2csICdcXFxcJCYnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICAgICAgICAgIC5qb2luKCd8JylcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm5ld1RhZ1xuICAgICAgICAgICAgICAgICAgICAgICAgLnNwbGl0KG5ldyBSZWdFeHAocGF0dGVybiwgJ2cnKSlcbiAgICAgICAgICAgICAgICAgICAgICAgIC5mb3JFYWNoKCh0YWcpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLm5ld1RhZyA9IHRhZ1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5jcmVhdGVUYWcoKVxuICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSxcbiAgICB9XG59XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQWUsU0FBUix1QkFBd0MsRUFBRSxPQUFPLFVBQVUsR0FBRztBQUNqRSxTQUFPO0FBQUEsSUFDSCxRQUFRO0FBQUEsSUFFUjtBQUFBLElBRUEsV0FBVyxXQUFZO0FBQ25CLFdBQUssU0FBUyxLQUFLLE9BQU8sS0FBSztBQUUvQixVQUFJLEtBQUssV0FBVyxJQUFJO0FBQ3BCO0FBQUEsTUFDSjtBQUVBLFVBQUksS0FBSyxNQUFNLFNBQVMsS0FBSyxNQUFNLEdBQUc7QUFDbEMsYUFBSyxTQUFTO0FBRWQ7QUFBQSxNQUNKO0FBRUEsV0FBSyxNQUFNLEtBQUssS0FBSyxNQUFNO0FBRTNCLFdBQUssU0FBUztBQUFBLElBQ2xCO0FBQUEsSUFFQSxXQUFXLFNBQVUsYUFBYTtBQUM5QixXQUFLLFFBQVEsS0FBSyxNQUFNLE9BQU8sQ0FBQyxRQUFRLFFBQVEsV0FBVztBQUFBLElBQy9EO0FBQUEsSUFFQSxPQUFPO0FBQUEsTUFDSCxDQUFDLFdBQVcsR0FBRztBQUFBLE1BQ2YsQ0FBQyxTQUFTLEdBQUc7QUFBQSxNQUNiLENBQUMsY0FBYyxFQUFFLE9BQU87QUFDcEIsWUFBSSxDQUFDLFNBQVMsR0FBRyxTQUFTLEVBQUUsU0FBUyxNQUFNLEdBQUcsR0FBRztBQUM3QyxnQkFBTSxlQUFlO0FBQ3JCLGdCQUFNLGdCQUFnQjtBQUV0QixlQUFLLFVBQVU7QUFBQSxRQUNuQjtBQUFBLE1BQ0o7QUFBQSxNQUNBLENBQUMsWUFBWSxJQUFJO0FBQ2Isa0JBQVUsTUFBTTtBQUNaLGdCQUFNLFVBQVUsVUFDWDtBQUFBLFlBQUksQ0FBQyxRQUNGLElBQUksUUFBUSwwQkFBMEIsTUFBTTtBQUFBLFVBQ2hELEVBQ0MsS0FBSyxHQUFHO0FBRWIsZUFBSyxPQUNBLE1BQU0sSUFBSSxPQUFPLFNBQVMsR0FBRyxDQUFDLEVBQzlCLFFBQVEsQ0FBQyxRQUFRO0FBQ2QsaUJBQUssU0FBUztBQUVkLGlCQUFLLFVBQVU7QUFBQSxVQUNuQixDQUFDO0FBQUEsUUFDVCxDQUFDO0FBQUEsTUFDTDtBQUFBLElBQ0o7QUFBQSxFQUNKO0FBQ0o7IiwKICAibmFtZXMiOiBbXQp9Cg==
